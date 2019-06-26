<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    //菜品管理
    $router->group(['prefix'=>'food'],function ($router){
        //外卖菜品池
        $router->get('takeOutPool','TakeOutFoolController@index');
        //新增外卖菜品
        $router->get('takeOutPool/create','TakeOutFoolController@create');
    });
    //智慧发现
    $router->group(['prefix'=>'discover'],function ($router){
        //智慧发现列表
        $router->get('/','DiscoverController@index');
        //新增发现
        $router->get('/create','DiscoverController@create');
        $router->post('/','DiscoverController@store');
        //编辑发现
        $router->get('/{id}/edit','DiscoverController@edit');
        $router->put('/{id}','DiscoverController@update');
        //删除发现
        $router->delete('/{id}','DiscoverController@destroy');
    });

});
