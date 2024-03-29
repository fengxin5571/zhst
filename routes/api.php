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
        //网上超市
        $api->group(['prefix'=>'marketFood'],function ($api){
            //网超菜品分类
            $api->get('category','MarketFoodController@category');
            //网超预定菜品
            $api->get('foods','MarketFoodController@foods');
        });
        //网上订餐
        $api->group(['prefix'=>'reserve'],function ($api){
            //网订类型
            $api->get('type','ReserveFoodController@type');
            //网订菜品分类
            $api->get('category','ReserveFoodController@category');
            //网订菜品
            $api->get('food','ReserveFoodController@food');
            //健康养生
            $api->get('health','ReserveFoodController@health');
            //养生详情
            $api->get('health/info','ReserveFoodController@shealthInfo');
            //厨艺交流
            $api->get('exchange','ReserveFoodController@exchange');
            //厨艺详情
            $api->get('exchange/info','ReserveFoodController@exchangeInfo');
            //周菜谱
            $api->get('/weekly/menu','ReserveMenuController@weekly');
            //今日菜谱领导
            $api->get('todayFood/leader','ReserveFoodController@todayLeader');
            //今日菜谱员工
            $api->get('todayFood/employ','ReserveFoodController@todayEmploy');
            //网订菜品点赞，取消
            $api->get('like','ReserveFoodController@like');
            //网订菜品差评，取消
            $api->get('bad','ReserveFoodController@bad');
            //网订菜品评论列表
            $api->get('comment','ReserveFoodController@comment');
            //网订菜品评论
            $api->post('comment/add','ReserveFoodController@addComment');
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
            //订单支付
            $api->post('pay','OrderController@orderPay');
            //提交网订预定
            $api->post('/reserve/add','OrderController@reserveAdd');
            //我的订单
            $api->get('my','OrderController@myOrder');
            //订单详情
            $api->get('details','OrderController@orderDetails');
            //取消订单
            $api->get('close','OrderController@closeOrder');
        });

        //个人中心
        $api->get('my','UserController@my');

        //智慧发现
        $api->group(['prefix'=>'discover'],function($api){
            //智慧发现接口
            $api->get('/','DdiscoverController@index');
        });
        //新品推荐
        $api->get('food/new','ReserveMenuController@new');

        //公告
        $api->get('notice','NoticeController@notice');
    });
    //获取菜品类型select联动
    $api->get('/food_type','FoodsController@type');
    //获取菜品的select联动
    $api->get('/foods','FoodsController@index');

});
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
