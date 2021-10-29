<?php

use mstodulski\router\RouteException;
use mstodulski\router\Router;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    private Router $router;
    
    public function setUp(): void
    {
        $this->router = new mstodulski\router\Router();
        $this->router->defineRoutes(getRoutes());
    }

    /** @throws RouteException */
    public function testGetRoutes()
    {
        $route = $this->router->getRouteByLink('/admin/category/test');
        $this->assertEquals('/admin/category/test', $route['linkSchema']);
        $this->assertEmpty($route['parameters']);

        $route = $this->router->getRouteByLink('/admin/category/123');
        $this->assertEquals('/admin/category/:id', $route['linkSchema']);
        $this->assertEquals('123', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/admin/categories/1');
        $this->assertEquals('/admin/categories/:page', $route['linkSchema']);
        $this->assertEquals('1', $route['parameters']['page']);

        $route = $this->router->getRouteByLink('/admin/category/1', 'POST');
        $this->assertEquals('/admin/category/:id', $route['linkSchema']);
        $this->assertEquals('1', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/admin/category/1', 'PUT');
        $this->assertEquals('/admin/category/:id', $route['linkSchema']);
        $this->assertEquals('1', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/ajax/update-category-hits', 'POST');
        $this->assertEquals('/ajax/update-category-hits', $route['linkSchema']);
        $this->assertEmpty($route['parameters']);

        $route = $this->router->getRouteByLink('/admin/category/123');
        $this->assertEquals('/admin/category/:id', $route['linkSchema']);
        $this->assertEquals('en', $route['lang']);
        $this->assertEquals('123', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/admin/kategoria/123');
        $this->assertEquals('/admin/kategoria/:id', $route['linkSchema']);
        $this->assertEquals('pl', $route['lang']);
        $this->assertEquals('123', $route['parameters']['id']);
    }

    /** @throws RouteException */
    public function testRoutesWithDefaultParameters()
    {
        $link = $this->router->createLinkFromSchema('ajaxAction3');
        $this->assertEquals('/ajax/update-category-count-1', $link);

        $link = $this->router->createLinkFromSchema('ajaxAction4', ['count' => 12]);
        $this->assertEquals('/ajax/update-category-count-2/12', $link);

        $link = $this->router->createLinkFromSchema('ajaxAction5', ['id' => 12]);
        $this->assertEquals('/ajax/update-category-count-3/3/12', $link);

        $link = $this->router->createLinkFromSchema('ajaxAction4', ['id' => 3, 'count' => 12]);
        $this->assertEquals('/ajax/update-category-count-2/12', $link);

        $link = $this->router->createLinkFromSchema('ajaxAction4', ['id' => 4, 'count' => 12]);
        $this->assertEquals('/ajax/update-category-count-2/12/4', $link);

        $link = $this->router->createLinkFromSchema('ajaxAction6');
        $this->assertEquals('/ajax/update-category-count-6/3/test', $link);

        $link = $this->router->createLinkFromSchema('ajaxAction6', ['id' => 3]);
        $this->assertEquals('/ajax/update-category-count-6/3/test', $link);

        $link = $this->router->createLinkFromSchema('ajaxAction6', ['id' => 5]);
        $this->assertEquals('/ajax/update-category-count-6/3/test/5', $link);

        $link = $this->router->createLinkFromSchema('ajaxAction6', ['count' => 12, 'id' => 5]);
        $this->assertEquals('/ajax/update-category-count-6/12/test/5', $link);
    }

    public function testGetNonExistentRoute()
    {
        $this->expectException(RouteException::class);
        $this->router->getRouteByLink('/admin/test/test/test');

        $this->expectException(RouteException::class);
        $this->router->getRouteByLink('/admin/categories/categories');
    }

    public function testGetNonExistentRoute2()
    {
        $this->expectException(RouteException::class);
        $this->router->getRouteByLink('/admin/categories/categories');
    }

    public function testGetExistentRouteWithNotValidMethod()
    {
        $this->expectException(RouteException::class);
        $this->router->getRouteByLink('/admin/categories/1', 'POST');
    }

    /** @throws RouteException */
    public function testCreateLink()
    {
        $link = $this->router->createLinkFromSchema('category', ['id' => 345], 'en');
        $this->assertEquals('/admin/category/345', $link);

        $link = $this->router->createLinkFromSchema('category');
        $this->assertEquals('/admin/kategoria', $link);

        $link = $this->router->createLinkFromSchema('categories', ['page' => 2]);
        $this->assertEquals('/admin/categories/2', $link);
    }

    public function testCreateNonExistentLink()
    {
        $this->expectException(RouteException::class);
        $this->router->createLinkFromSchema('non_existent');
    }

    public function testCreateExistentLinkWithNoParameters()
    {
        $this->expectException(RouteException::class);
        $this->router->createLinkFromSchema('articles');
    }

    public function testCreateExistentLinkWithNotValidParameter()
    {
        $this->expectException(RouteException::class);
        $this->router->createLinkFromSchema('categories', ['page' => 'not_int']);
    }

    /** @throws RouteException */
    public function testGetRouteByLinkNotDefinedDefaultParameters()
    {
        $route = $this->router->getRouteByLink('/ajax/update-category-count-1', 'POST');
        $this->assertEquals('/ajax/update-category-count-1/:count/:id', $route['linkSchema']);
        $this->assertEquals('3', $route['parameters']['count']);
        $this->assertEquals('4', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/ajax/update-category-count-1/1', 'POST');
        $this->assertEquals('/ajax/update-category-count-1/:count/:id', $route['linkSchema']);
        $this->assertEquals('1', $route['parameters']['count']);
        $this->assertEquals('4', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/ajax/update-category-count-1/1/2', 'POST');
        $this->assertEquals('/ajax/update-category-count-1/:count/:id', $route['linkSchema']);
        $this->assertEquals('1', $route['parameters']['count']);
        $this->assertEquals('2', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/ajax/update-category-count-6/3/test', 'POST');
        $this->assertEquals('/ajax/update-category-count-6/:count/test/:id', $route['linkSchema']);
        $this->assertEquals('3', $route['parameters']['count']);
        $this->assertEquals('3', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/ajax/update-category-count-6/3/test/3', 'POST');
        $this->assertEquals('/ajax/update-category-count-6/:count/test/:id', $route['linkSchema']);
        $this->assertEquals('3', $route['parameters']['count']);
        $this->assertEquals('3', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/ajax/update-category-count-6/3/test/5', 'POST');
        $this->assertEquals('/ajax/update-category-count-6/:count/test/:id', $route['linkSchema']);
        $this->assertEquals('3', $route['parameters']['count']);
        $this->assertEquals('5', $route['parameters']['id']);

        $route = $this->router->getRouteByLink('/ajax/update-category-count-6/12/test/5', 'POST');
        $this->assertEquals('/ajax/update-category-count-6/:count/test/:id', $route['linkSchema']);
        $this->assertEquals('12', $route['parameters']['count']);
        $this->assertEquals('5', $route['parameters']['id']);
    }

    public function testGetRouteByLinkNotDefinedDefaultParametersNoDefinedParameter()
    {
        $this->expectException(RouteException::class);
        $this->router->getRouteByLink('/ajax/update-category-count-7/7/test', 'POST');
    }
}
