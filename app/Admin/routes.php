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

        //外卖菜品分类
        $router->get('takeOutCategroy','TakeFoodCategroyController@index');
        //新增菜品分类
        $router->get('takeOutCategroy/create','TakeFoodCategroyController@create');
        $router->post('takeOutCategroy','TakeFoodCategroyController@store');
        //编辑菜品分类
        $router->get('takeOutCategroy/{id}/edit','TakeFoodCategroyController@edit');
        $router->put('takeOutCategroy/{id}','TakeFoodCategroyController@update');
        //删除菜品分类
        $router->delete('takeOutCategroy/{id}','TakeFoodCategroyController@destroy');

        //外卖菜品标签
        $router->get('takeOutTag','TakeFoodTagController@index');
        //新增外卖菜品标签
        $router->get('takeOutTag/create','TakeFoodTagController@create');
        $router->post('takeOutTag','TakeFoodTagController@store');
        //编辑外卖菜品标签
        $router->get('takeOutTag/{id}/edit','TakeFoodTagController@edit');
        $router->put('takeOutTag/{id}','TakeFoodTagController@update');
        //删除外卖菜品标签
        $router->delete('takeOutTag/{id}','TakeFoodTagController@destroy');

        //外卖菜品
        $router->get('takeOutPool','TakeOutFoolController@index');
        //新增外卖菜品
        $router->get('takeOutPool/create','TakeOutFoolController@create');
        $router->post('takeOutPool','TakeOutFoolController@store');
        //编辑外卖菜品
        $router->get('takeOutPool/{id}/edit','TakeOutFoolController@edit');
        $router->put('takeOutPool/{id}','TakeOutFoolController@update');
        //删除外卖菜品
        $router->delete('takeOutPool/{id}','TakeOutFoolController@destroy');

        //网订类型管理
        $router->get('/reserveType','ReserveTypeController@index');
        //编辑网订类型
        $router->get('/reserveType/{id}/edit','ReserveTypeController@edit');
        $router->put('/reserveType/{id}','ReserveTypeController@update');

        //网订分类管理
        $router->get('/reserveFoodCategory','ReserveFoodCategoryController@index');
        //新增网订菜品分类
        $router->get('/reserveFoodCategory/create','ReserveFoodCategoryController@create');
        $router->post('/reserveFoodCategory','ReserveFoodCategoryController@store');
        //编辑网订菜品分类
        $router->get('/reserveFoodCategory/{id}/edit','ReserveFoodCategoryController@edit');
        $router->put('/reserveFoodCategory/{id}','ReserveFoodCategoryController@update');
        //删除网订菜品分类
        $router->delete('/reserveFoodCategory/{id}','ReserveFoodCategoryController@destroy');

        //网订菜品标签
        $router->get('/reserveTag','ReserveFoodTagController@index');
        //新增网订菜品标签
        $router->get('/reserveTag/create','ReserveFoodTagController@create');
        $router->post('reserveTag','ReserveFoodTagController@store');
        //编辑网订菜品标签
        $router->get('/reserveTag/{id}/edit','ReserveFoodTagController@edit');
        $router->put('/reserveTag/{id}','ReserveFoodTagController@update');
        //删除网订菜品标签
        $router->delete('/reserveTag/{id}','ReserveFoodTagController@destroy');

        //网订菜品
        $router->get('/reservePool','ReserveFoodPoolController@index');
        //新增网订菜品
        $router->get('/reservePool/create','ReserveFoodPoolController@create');
        $router->post('reservePool','ReserveFoodPoolController@store');
        //编辑菜品
        $router->get('/reservePool/{id}/edit','ReserveFoodPoolController@edit');
        $router->put('/reservePool/{id}','ReserveFoodPoolController@update');
        //删除菜品
        $router->delete('/reservePool/{id}','ReserveFoodPoolController@destroy');


    });
    //套餐管理
    $router->group(['prefix'=>'package'],function ($router){
        //套餐列表
        $router->get('/','PackageFoodController@index');
        //新增套餐
        $router->get('/create','PackageFoodController@create');
        $router->post('/','PackageFoodController@store');
        //编辑套餐
        $router->get('/{id}/edit','PackageFoodController@edit');
        $router->put('/{id}','PackageFoodController@update');
        //删除套餐
        $router->delete('/{id}','PackageFoodController@destroy');
    });
    //订单管理
    $router->group(['prefix'=>'order'],function($router){
        //订单列表
        $router->get('/','OrderController@index');
        //查看订单
        $router->get('/{id}','OrderController@show');
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
