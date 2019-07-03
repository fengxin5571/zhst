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
use App\Model\TakeFoodReplyRelation;
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
        $recommendFood['food_list']->each(function($item,$key){
            $item->is_like=TakeFoodReplyRelation::where(['userid'=>$this->user['userId'],'t_food_id'=>$item->id])->count()?true:false;
        });
        return $this->successResponse($recommendFood);
    }

    /**
     * 外卖菜品点赞取消
     * @param Request $request
     * @return mixed
     */
    public function like(Request $request){
        $food_id=$request->get('food_id');
        $status=$request->get('status',1);
        if($food_id){
            if($status==1){//点赞
                if(TakeFoodReplyRelation::firstOrCreate(['userid'=>$this->user['userId']],['t_food_id'=>$food_id])){
                    TakeFoodPool::where('id',$food_id)->increment('likeCount',1);
                }
                return $this->successResponse('点赞成功');
            }elseif($status==2){//取消
                TakeFoodReplyRelation::where(['userid'=>$this->user['userId'],'t_food_id'=>$food_id])->delete();
                TakeFoodPool::where('id',$food_id)->decrement('likeCount',1);
                return $this->successResponse('取消点赞成功');
            }


        }
        return $this->response->error('点赞失败',403);
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
        $fields=['id','cid','name','food_type','description','food_image','ot_price','point','price','sellCount','likeCount'];
        $food_list['food_list']=TakeFoodCategory::get(['id','cat_name']);
        $food_list['food_list']->each(function($item,$key) use($fields){
            $item['foods']=TakeFoodPool::where('cid',$item->id)->isShow()->groupBy('cid','id')->get($fields)->each(function ($item,$key){
                $item->num=0;
            });
        });
        return $this->successResponse($food_list);
    }
}