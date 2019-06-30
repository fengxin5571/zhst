<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/30
 * Time: 1:59 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\TakeFoodCategory;
use App\Model\TakeFoodPool;
use Illuminate\Http\Request;

class TakeOutFoodController extends Controller{
    /**
     * 特别推荐菜品
     * @param Request $request
     * @return mixed
     */
    public function recommend(Request $request){
        $fields=['id','name','description','food_image','price','likeCount'];
        $recommendFood['food_count']=TakeFoodPool::isShow()->isRecommend()->count();
        $recommendFood['food_list']=TakeFoodPool::isShow()->isRecommend()
            ->forPage($request->get('page',1),$request->get('limit',Controller::LIMIT))->get($fields);
        return $this->successResponse($recommendFood);
    }
    /**
     * 外卖菜品分类
     * @param Request $request
     * @return mixed
     */
    public function category(Request $request){
        $categoryList['category_list']=TakeFoodCategory::get(['id','cat_name']);
        return $this->successResponse($categoryList);
    }

    /**
     * 外卖菜品
     * @param Request $request
     * @return mixed
     */
    public function foods(Request $request){
        $fields=['id','cid','description','food_image','ot_price','point','price','sellCount','likeCount'];
        $food_list['food_list']=TakeFoodCategory::get(['id','cat_name']);
        $food_list['food_list']->each(function($item,$key) use($fields){
            $item['foods']=TakeFoodPool::where('cid',$item->id)->isShow()->groupBy('cid','id')->get($fields);
        });
        return $this->successResponse($food_list);
    }
}