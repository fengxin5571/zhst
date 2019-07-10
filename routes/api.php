<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace'=>'App\Http\Controllers\Api',
    'middleware'=>['bindings','cors'],
],function($api) {
    $api->group(['middleware'=>'authToken'],function($api){//用户验证中间件
        $api->get('/test','testController@index');
        //外卖预定
        $api->group(['prefix'=>'takeOut'],function($api){
            //特色推荐
            $api->get('recommend','TakeOutFoodController@recommend');
            //外卖菜品分类
            $api->get('category','TakeOutFoodController@category');
            //外卖预定菜品
            $api->get('foods','TakeOutFoodController@foods');
            //外卖菜品点赞,取消
            $api->get('like','TakeOutFoodController@like');
        });

        //网上订餐
        $api->group(['prefix'=>'reserve'],function ($api){

        });

        //购物车
        $api->group(['prefix'=>'cart'],function ($api){
            //查看购物车
            $api->get('/','CartController@index');
            //查看购物车总数
            $api->get('count','CartController@Cartnum');
            //添加，减少购物车
            $api->post('add','CartController@add');
            //删除购物车
            $api->post('remove','CartController@remove');

        });

        //订单
        $api->group(['prefix'=>'order'],function ($api){
            //订单结算
            $api->get('confirm','OrderController@confirmOrder');
            //创建订单
            $api->post('add','OrderController@add');
        });

        //智慧发现
        $api->group(['prefix'=>'discover'],function($api){
            //智慧发现接口
            $api->get('/','DdiscoverController@index');
        });

    });
    //获取菜品类型select联动
    $api->get('/food_type','FoodsController@type');
    //获取菜品的select联动
    $api->get('/foods','FoodsController@index');

});
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
