<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/30
 * Time: 1:59 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Cart;
use App\Model\ReserveFoodPool;
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
//        $recommendFood['food_count']=ReserveFoodPool::isShow()->count();
        $recommendFood['food_list']=ReserveFoodPool::isShow()
            ->orderBy('likeCount','desc')->get($fields);
//        $recommendFood['food_list']->each(function($item,$key){
//            $item->is_like=TakeFoodReplyRelation::where(['userid'=>$this->user['userId'],'t_food_id'=>$item->id])->count()?true:false;
//        });
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
                if(TakeFoodReplyRelation::where(['userid'=>$this->user['userId'],'t_food_id'=>$food_id])->count()==0){
                    TakeFoodReplyRelation::create(['userid'=>$this->user['userId'],'t_food_id'=>$food_id]);
                    TakeFoodPool::where('id',$food_id)->increment('likeCount',1);
                }
                return $this->successResponse('点赞成功');
            }elseif($status==2){//取消
                TakeFoodReplyRelation::where(['userid'=>$this->user['userId'],'t_food_id'=>$food_id])->delete();
                if(TakeFoodPool::where('id',$food_id)->value('likeCount')>0){
                    TakeFoodPool::where('id',$food_id)->decrement('likeCount',1);
                }
                return $this->successResponse('取消点赞成功');
            }


        }
        return $this->response->error('点赞失败',$this->forbidden_code);
    }
    /**
     * 外卖菜品分类
     * @param Request $request
     * @return mixed
     */
    public function category(Request $request){
        $categoryList['category_list']=TakeFoodCategory::get(['id','cat_name']);
        $cart_list=Cart::where(['userid'=>$this->user['userId'],'type'=>1,'food_type'=>1])->get();
        //dd($cart_num);
        $categoryList['category_list']->each(function($item,$key) use($cart_list){
            $item->cart_num=0;
            foreach ($cart_list as $cart){
                if($cart->takeFood->foodCategory->id==$item->id){
                    $item->cart_num=$item->cart_num+$cart->cart_num;
                }
            }

        });
        return $this->successResponse($categoryList);
    }

    /**
     * 外卖菜品
     * @param Request $request
     * @return mixed
     */
    public function foods(Request $request){
        $fields=['id','cid','name','food_type','description','food_image','ot_price','point','price','sellCount','likeCount','limit','stock'];
        $food_list['food_list']=TakeFoodCategory::get(['id','cat_name']);
        $food_list['food_list']->each(function($item,$key) use($fields){
            $item['foods']=TakeFoodPool::where('cid',$item->id)->isShow()->groupBy('cid','id')->get($fields)->each(function ($item,$key){
                $item->num=0;
            });
        });
        return $this->successResponse($food_list);
    }
}