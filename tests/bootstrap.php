<?php


require_once 'vendor/autoload.php';

function getRoutes()
{
    $routes['login']['linkSchema'] = '/admin/login';
    $routes['login']['method'] = ['GET', 'POST'];
    $routes['login']['options']['controller'] = 'test\router\helpers\Controller::index';

    $routes['articles']['linkSchema'] = '/admin/articles/:page';
    $routes['articles']['method'] = ['GET'];
    $routes['articles']['options']['controller'] = 'test\router\helpers\Controller::articles';
    $routes['articles']['options']['requirements']['regex']['page'] = '/^\d+$/';

    $routes['article']['linkSchema'] = '/admin/article/:id';
    $routes['article']['method'] = ['GET'];
    $routes['article']['options']['controller'] = 'test\router\helpers\Controller::article';

    $routes['articleUpdate']['linkSchema'] = '/admin/article/:id';
    $routes['articleUpdate']['method'] = ['POST', 'PUT'];
    $routes['articleUpdate']['options']['controller'] = 'test\router\helpers\Controller::articleUpdate';

    $routes['articleDelete']['linkSchema'] = '/admin/article/:id';
    $routes['articleDelete']['method'] = ['DELETE'];
    $routes['articleDelete']['options']['controller'] = 'test\router\helpers\Controller::articleDelete';

    $routes['categories']['linkSchema'] = '/admin/categories/:page';
    $routes['categories']['method'] = ['GET'];
    $routes['categories']['options']['controller'] = 'test\router\helpers\Controller::categories';
    $routes['categories']['options']['requirements']['regex']['page'] = '/^\d+$/';
    $routes['categories']['options']['defaults']['page'] = 1;

    $routes['categoryTest']['linkSchema'] = '/admin/category/test';
    $routes['categoryTest']['method'] = ['GET'];
    $routes['categoryTest']['options']['controller'] = 'test\router\helpers\Controller::categoryTest';

    $routes['category']['linkSchema']['pl'] = '/admin/kategoria/:id';
    $routes['category']['linkSchema']['en'] = '/admin/category/:id';
    $routes['category']['method'] = ['GET'];
    $routes['category']['options']['controller'] = 'test\router\helpers\Controller::category';
    $routes['category']['options']['requirements']['regex']['id'] = '/^\d+$/';
    $routes['category']['options']['defaults']['id'] = 123;

    $routes['categoryUpdate']['linkSchema'] = '/admin/category/:id';
    $routes['categoryUpdate']['method'] = ['POST', 'PUT'];
    $routes['categoryUpdate']['options']['controller'] = 'test\router\helpers\Controller::categoryUpdate';

    $routes['categoryDelete']['linkSchema'] = '/admin/category/:id';
    $routes['categoryDelete']['method'] = ['DELETE'];
    $routes['categoryDelete']['options']['controller'] = 'test\router\helpers\Controller::categoryDelete';

    $routes['ajaxAction1']['linkSchema'] = '/ajax/update-article-hits';
    $routes['ajaxAction1']['method'] = ['POST'];
    $routes['ajaxAction1']['options']['controller'] = 'test\router\helpers\Controller::updateArticleHits';

    $routes['ajaxAction2']['linkSchema'] = '/ajax/update-category-hits';
    $routes['ajaxAction2']['method'] = ['POST'];
    $routes['ajaxAction2']['options']['controller'] = 'test\router\helpers\Controller::updateCategoryHits';

    $routes['ajaxAction3']['linkSchema'] = '/ajax/update-category-count-1/:count/:id';
    $routes['ajaxAction3']['method'] = ['POST'];
    $routes['ajaxAction3']['options']['controller'] = 'test\router\helpers\Controller::updateCategoryCount1';
    $routes['ajaxAction3']['options']['defaults']['count'] = 3;
    $routes['ajaxAction3']['options']['defaults']['id'] = 4;

    $routes['ajaxAction4']['linkSchema'] = '/ajax/update-category-count-2/:count/:id';
    $routes['ajaxAction4']['method'] = ['POST'];
    $routes['ajaxAction4']['options']['controller'] = 'test\router\helpers\Controller::updateCategoryCount2';
    $routes['ajaxAction4']['options']['defaults']['id'] = 3;

    $routes['ajaxAction5']['linkSchema'] = '/ajax/update-category-count-3/:count/:id';
    $routes['ajaxAction5']['method'] = ['POST'];
    $routes['ajaxAction5']['options']['controller'] = 'test\router\helpers\Controller::updateCategoryCount3';
    $routes['ajaxAction5']['options']['defaults']['count'] = 3;

    $routes['ajaxAction6']['linkSchema'] = '/ajax/update-category-count-6/:count/test/:id';
    $routes['ajaxAction6']['method'] = ['POST'];
    $routes['ajaxAction6']['options']['controller'] = 'test\router\helpers\Controller::updateCategoryCount6';
    $routes['ajaxAction6']['options']['defaults']['count'] = 3;
    $routes['ajaxAction6']['options']['defaults']['id'] = 3;

    $routes['ajaxAction7']['linkSchema'] = '/ajax/update-category-count-7/:count/test/:id';
    $routes['ajaxAction7']['method'] = ['POST'];
    $routes['ajaxAction7']['options']['controller'] = 'test\router\helpers\Controller::updateCategoryCount7';
    $routes['ajaxAction7']['options']['defaults']['count'] = 3;

    return $routes;
}
