<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/9/9
 * Time: 4:35 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\MarketFoodPool;
use App\Model\ReserveFoodPool;
use App\Model\ReserveFoodReplyRelation;
use App\Model\ReserveMenu;
use App\Model\TakeFoodPool;
use Illuminate\Http\Request;

class ReserveMenuController extends Controller{
    /**
     * 周菜谱
     * @param Request $request
     * @return mixed
     */
    public function weekly(Request $request){
        $cid=$request->input('cid');
        $week=$request->input('week');
        $type=$request->input('type');
        if(!$cid||!$week||!$type){
            return $this->response->error('参数错误',$this->forbidden_code);
        }
        $fields=['id','cid','name','food_type','description','food_image','point','price','likeCount','cook','cook_image','cook_speciality','cook_group'];
        $week_food_ids=ReserveMenu::where(['reserve_type'=>$type])->whereRaw("find_in_set('".$week."',weekly)")->pluck('food_id');
        $data['food_list']=ReserveFoodPool::whereIn('id',$week_food_ids)->where(['is_show'=>1,'cid'=>$cid])->get($fields);
        $data['food_list']->each(function($item,$key){
            $item->is_like=ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$item->id,'type'=>'like'])->count()>0?true:false;
            $item->is_bad=ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$item->id,'type'=>'bad'])->count()>0?true:false;;
        });
        return $this->successResponse($data);
    }

    /**
     * 新品推荐
     * @param Request $request
     * @return mixed
     */
    public function new(Request $request){
        $type=$request->input('type',1);
        $fields=['id','cid','name','food_type','description','food_image','price','likeCount'];
        if($type==1){//网订菜品
            $data['food_list']=ReserveFoodPool::where('is_new',1)->isShow()->orderBy('likeCount','desc')->get($fields);
        }elseif ($type==2){//外卖菜品
            $data['food_list']=TakeFoodPool::where('is_new',1)->isShow()->orderBy('likeCount','desc')->get($fields);
        }elseif ($type==3){//网超
            $data['food_list']=MarketFoodPool::where('is_new',1)->isShow()->orderBy('likeCount','desc')->get($fields);
        }
        return $this->successResponse($data);
    }
}