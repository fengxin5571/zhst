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
        //导入菜品
        $router->get('/takeOutPool/import','TakeOutFoolController@import')->name('takeOut.import');
        $router->post('takeOutPool/import','TakeOutFoolController@importPost')->name('takeOut.import.post');

        //网超菜品分类
        $router->get('marketFoodCategroy','MarketFoodCategroyController@index');
        //新增菜品分类
        $router->get('marketFoodCategroy/create','MarketFoodCategroyController@create');
        $router->post('marketFoodCategroy','MarketFoodCategroyController@store');
        //编辑菜品分类
        $router->get('marketFoodCategroy/{id}/edit','MarketFoodCategroyController@edit');
        $router->put('marketFoodCategroy/{id}','MarketFoodCategroyController@update');
        //删除菜品分类
        $router->delete('marketFoodCategroy/{id}','MarketFoodCategroyController@destroy');

        //网超菜品标签
        $router->get('marketOutTag','MarketFoodTagController@index');
        //新增网超菜品标签
        $router->get('marketOutTag/create','MarketFoodTagController@create');
        $router->post('marketOutTag','MarketFoodTagController@store');
        //编辑网超菜品标签
        $router->get('marketOutTag/{id}/edit','MarketFoodTagController@edit');
        $router->put('marketOutTag/{id}','MarketFoodTagController@update');
        //删除网超菜品标签
        $router->delete('marketOutTag/{id}','MarketFoodTagController@destroy');

        //网超菜品
        $router->get('marketFoodPool','MarketFoodPoolController@index');
        //新增网超菜品
        $router->get('marketFoodPool/create','MarketFoodPoolController@create');
        $router->post('marketFoodPool','MarketFoodPoolController@store');
        //编辑网超菜品
        $router->get('marketFoodPool/{id}/edit','MarketFoodPoolController@edit');
        $router->put('marketFoodPool/{id}','MarketFoodPoolController@update');
        //删除网超菜品
        $router->delete('marketFoodPool/{id}','MarketFoodPoolController@destroy');
        //导入菜品
        $router->get('marketFoodPool/import','MarketFoodPoolController@import')->name('market.import');
        $router->post('marketFoodPool/import','MarketFoodPoolController@importPost')->name('market.import.post');
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
        //网订菜品批量操作
        $router->post('/reservePool/today','ReserveFoodPoolController@today');
        //新增网订菜品
        $router->get('/reservePool/create','ReserveFoodPoolController@create');
        $router->post('reservePool','ReserveFoodPoolController@store');
        //编辑菜品
        $router->get('/reservePool/{id}/edit','ReserveFoodPoolController@edit');
        $router->put('/reservePool/{id}','ReserveFoodPoolController@update');
        //删除菜品
        $router->delete('/reservePool/{id}','ReserveFoodPoolController@destroy');
        //导入菜品
        $router->get('reservePool/import','ReserveFoodPoolController@import')->name('reserve.import');
        $router->post('reservePool/import','ReserveFoodPoolController@importPost')->name('reserve.import.post');
        //网订菜品评论
        $router->get('reservePool/comment','ReserveFoodReplyController@index');
        //删除网订菜品评论
        $router->delete('reservePool/comment/{id}','ReserveFoodReplyController@destroy');

        //网订菜谱
        $router->get('/reserveMenu','ReserveMenuController@index');
        //新增菜谱
        $router->get('/reserveMenu/create','ReserveMenuController@create');
        $router->post('/reserveMenu','ReserveMenuController@store');
        //删除菜谱
        $router->delete('/reserveMenu/{id}','ReserveMenuController@destroy');

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
        //删除外卖订单
        $router->delete('/{id}','OrderController@destroy');
        //网超订单
        $router->get('/market/list','MarketOrderController@index');
        //查看网超订单
        $router->get('/market/list/{id}','MarketOrderController@show');
        //删除网订订单
        $router->delete('/market/list/{id}','MarketOrderController@destroy');

        //网订预定订单
        $router->get('/reserve/list','ReserveOrderController@index');
        //确认网订订单
        $router->get('/reserve/success/{id}','ReserveOrderController@success');
        //删除网订订单
        $router->delete('/reserve/list/{id}','ReserveOrderController@destroy');

    });
    //公告
    $router->group(['prefix'=>'notice'],function ($router){
        //公告列表
        $router->get('/','NoticeController@index');
        //新增公告
        $router->get('/create','NoticeController@create');
        $router->post('/','NoticeController@store');
        //编辑公告
        $router->get('/{id}/edit','NoticeController@edit');
        $router->put('/{id}','NoticeController@update');
        //删除公告
        $router->delete('/{id}','NoticeController@destroy');
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
    //系统设置
    $router->get('settings','FormController@setting');
});
