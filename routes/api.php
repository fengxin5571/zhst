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
    'middleware'=>['bindings'],
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
        });
        //智慧发现
        $api->group(['prefix'=>'discover'],function($api){
            //智慧发现接口
            $api->get('/','DdiscoverController@index');
        });

    });
});
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
