<?php

/**
 * This file is part of the EasyCore package.
 *
 * (c) Marcin Stodulski <marcin.stodulski@devsprint.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mstodulski\router;

class Router
{
    private static array $routes = [];

    public function defineRoutes(array $routes)
    {
        foreach ($routes as $routeName => $route) {
            self::$routes[$routeName] = $route;
        }
    }

    private function getPossibleCounts(array $definedLinkArray, array $route) : array
    {
        $possibleCounts[] = count($definedLinkArray);

        $index = count($definedLinkArray) - 1;
        for ($i = count($definedLinkArray) - 1; $i >=0; $i--) {
            if (str_starts_with($definedLinkArray[$i], ':')) {
                $parameterName = ltrim($definedLinkArray[$i], ':');
                if (isset($route['options']['defaults']) && array_key_exists($parameterName, $route['options']['defaults'])) {
                    if ($index != $i) {
                        break;
                    } else {
                        $possibleCounts[] = $i;
                        $index--;
                    }
                }
            }
        }

        return $possibleCounts;
    }

    private function checkIfLinkMatch(array $currentUrlArray, array $routeParameters, string $method) : ?array
    {
        $definedLink = trim($routeParameters['linkSchema'],"/");
        $definedLink = trim($definedLink,"/");
        $definedLinkArray = explode('/', $definedLink);

        $possibleCounts = $this->getPossibleCounts($definedLinkArray, $routeParameters);

        if (in_array(count($currentUrlArray), $possibleCounts)) {

            $parametersMatch = true;
            $parameters = [];

            foreach ($definedLinkArray as $index => $definedUrlComponent) {
                if (isset($currentUrlArray[$index])) {
                    if (str_starts_with($definedUrlComponent, ':')) {
                        $parameterName = ltrim($definedUrlComponent, ':');
                        $parameters[$parameterName] = $currentUrlArray[$index];
                        if (isset($routeParameters['options']['requirements']['regex'][$parameterName])) {
                            if (!preg_match($routeParameters['options']['requirements']['regex'][$parameterName], $currentUrlArray[$index])) {
                                $parametersMatch = false;
                            }
                        }
                    } else {
                        if ($currentUrlArray[$index] != $definedUrlComponent) {
                            $parametersMatch = false;
                            break;
                        }
                    }
                } else {
                    $parameterName = ltrim($definedUrlComponent, ':');
                    if (isset($routeParameters['options']['defaults']) && array_key_exists($parameterName, $routeParameters['options']['defaults'])) {
                        $parameters[$parameterName] = $routeParameters['options']['defaults'][$parameterName];
                    } else {
                        $parametersMatch = false;
                        break;
                    }
                }
            }

            if ($parametersMatch) {
                if (
                    (is_array($routeParameters['method']) && (in_array($method, $routeParameters['method']))) ||
                    ($routeParameters['method'] == $method)
                ) {
                    $routeParameters['parameters'] = $parameters;
                    return $routeParameters;
                }
            }
        }

        return null;
    }

    /** @throws RouteException */
    public function getRouteByLink(string $link, string $method = 'GET') : array
    {
        $currentUrl = trim($link,"/");
        $currentUrlArray = explode('/', $currentUrl);

        foreach (self::$routes as $routeParameters) {
            if (is_array($routeParameters['linkSchema'])) {
                $routeParametersClone = $routeParameters;
                foreach ($routeParameters['linkSchema'] as $lang => $linkSchema) {
                    $routeParametersClone['linkSchema'] = $linkSchema;
                    $matchedRoute = $this->checkIfLinkMatch($currentUrlArray, $routeParametersClone, $method);
                    if ($matchedRoute !== null) {
                        $matchedRoute['lang'] = $lang;
                        return $matchedRoute;
                    }
                }
            } else {
                $matchedRoute = $this->checkIfLinkMatch($currentUrlArray, $routeParameters, $method);
                if ($matchedRoute !== null) {
                    return $matchedRoute;
                }
            }
        }

        throw new RouteException('Searched link was not found: ' . $currentUrl);
    }

    /** @throws RouteException */
    public function createLinkFromSchema(string $schema, array $parameters = [], string $lang = null) : string
    {
        if (isset(self::$routes[$schema])) {
            $route = self::$routes[$schema];

            if (is_array($route['linkSchema'])) {
                $linkSchema = $route['linkSchema'][$lang] ?? reset($route['linkSchema']);
            } else {
                $linkSchema = $route['linkSchema'];
            }

            $definedLink = trim($linkSchema,"/");
            $definedLinkArray = explode('/', $definedLink);

            $index = count($definedLinkArray) - 1;
            for ($i=count($definedLinkArray) - 1; $i>=0; $i--) {
                if (str_starts_with($definedLinkArray[$i], ':')) {
                    $parameterName = ltrim($definedLinkArray[$i], ':');
                    if (
                        isset($route['options']['defaults'][$parameterName]) &&
                        (
                            !isset($parameters[$parameterName]) ||
                            (isset($parameters[$parameterName]) && ($parameters[$parameterName] == $route['options']['defaults'][$parameterName]))
                        )
                    ) {
                        if ($index != $i) {
                            break;
                        } else {
                            unset($definedLinkArray[$i]);
                            $index--;
                        }
                    }
                }
            }

            foreach ($definedLinkArray as $index => $definedLinkComponent) {
                if (str_starts_with($definedLinkComponent, ':')) {
                    $parameterName = ltrim($definedLinkComponent, ':');
                    if (isset($parameters[$parameterName])) {
                        if (isset($route['options']['requirements']['regex'][$parameterName])) {
                            if (!preg_match($route['options']['requirements']['regex'][$parameterName], $parameters[$parameterName])) {
                                throw new RouteException('Parameter value: ' . $parameters[$parameterName] . ' does not satisfy the regex: ' . $route['options']['requirements']['regex'][$parameterName]);
                            }
                        }

                        $definedLinkArray[$index] = $parameters[$parameterName];
                    } else {
                        if (isset($route['options']['defaults']) && array_key_exists($parameterName, $route['options']['defaults'])) {
                            $definedLinkArray[$index] = $route['options']['defaults'][$parameterName];
                        } else {
                            throw new RouteException('Parameter :' . $parameterName . ' is not defined and default value is not configured.');
                        }
                    }
                }
            }

            return '/' . trim(implode('/', $definedLinkArray), '/');
        } else {
            throw new RouteException('Searched route was not found: ' . $schema);
        }
    }
}
