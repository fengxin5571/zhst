<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/9/8
 * Time: 11:15 AM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Cart;
use App\Model\MarketFoodCategory;
use App\Model\MarketFoodPool;
use Illuminate\Http\Request;
class MarketFoodController extends Controller{
    /**
     * 网超菜品分类
     * @param Request $request
     * @return mixed
     */
    public function category(Request $request){
        $categoryList['category_list']=MarketFoodCategory::get(['id','cat_name']);
        $cart_list=Cart::where(['userid'=>$this->user['userId'],'type'=>3,'food_type'=>1])->get();
        $categoryList['category_list']->each(function($item,$key) use($cart_list){
            $item->cart_num=0;
            foreach ($cart_list as $cart){
                if($cart->marketFood->foodCategory->id==$item->id){
                    $item->cart_num=$item->cart_num+$cart->cart_num;
                }
            }

        });
        return $this->successResponse($categoryList);
    }
    /**
     * 网超菜品
     * @param Request $request
     * @return mixed
     */
    public function foods(Request $request){
        $fields=['id','cid','name','food_type','description','food_image','ot_price','point','price','sellCount','likeCount'];
        $food_list['food_list']=MarketFoodCategory::get(['id','cat_name']);
        $food_list['food_list']->each(function($item,$key) use($fields){
            $item['foods']=MarketFoodPool::where('cid',$item->id)->isShow()->groupBy('cid','id')->get($fields)->each(function ($item,$key){
                $item->num=0;
            });
        });
        return $this->successResponse($food_list);
    }
}