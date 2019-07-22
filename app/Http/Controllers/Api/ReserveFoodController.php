<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/18
 * Time: 2:26 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\ReserveFoodCategory;
use App\Model\ReserveFoodPool;
use App\Model\ReserveFoodReplyRelation;
use App\Model\ReserveMenu;
use App\Model\ReserveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReserveFoodController extends Controller{
    /**
     * 网订类型
     * @return mixed
     */
    public function type(){
        $data['type_list']=ReserveType::get(['id','reserve_type_name','reserve_type_image']);
        return $this->successResponse($data);
    }

    /**
     * 网订菜品分类
     * @return mixed
     */
    public function category(){
        $categoryList['category_list']=ReserveFoodCategory::get(['id','cat_name']);
        return $this->successResponse($categoryList);
    }
    /**
     * 网订菜品
     * @param Request $request
     * @return mixed
     */
    public function food(Request $request){
       $message=[
           'type_id.required'=>'类型id不能为空',
           'type_id.numeric'=>'类型id需是数字'
       ];
        $validator=Validator::make($request->all(),[
            'type_id'=>'required|numeric'
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        //获取当天星期几
        $week=date('w');
        $fields=['id','cid','name','food_type','description','food_image','ot_price','point','price','sellCount','likeCount'];
        $week_food_ids=ReserveMenu::where('reserve_type',$request->input('type_id'))->whereRaw("find_in_set('".$week."',weekly)")->pluck('food_id');
        $data['food_list']=ReserveFoodCategory::get(['id','cat_name']);
        $data['food_list']->each(function($item,$key)use ($week_food_ids,$fields){
            $item['foods']=ReserveFoodPool::whereIn('id',$week_food_ids)->where(['is_show'=>1,'cid'=>$item->id])->get($fields);
        });
        return $this->successResponse($data);
    }

    /**
     * 今日菜谱
     * @param Request $request
     * @return mixed
     */
    public function today(Request $request){
        $today_type=$request->input('type',0);
        $data['food_list']=ReserveFoodPool::where('is_today',$today_type)->isShow()->get([
            'id','cid','name','description','cook','food_image','price','ot_price','point','likeCount'
            ]);
        $data['food_list']->each(function($item,$key){
            $item->is_like=ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$item->id])->count()>0?true:false;
        });
        return $this->successResponse($data);
    }

    /**
     * 网订菜品点赞，取消
     * @param Request $request
     * @return mixed
     */
    public function like(Request $request){
        $food_id=$request->get('food_id');
        $status=$request->get('status',1);
        if($food_id){
            if($status==1){//点赞
                if(ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$food_id])->count()==0){
                    ReserveFoodReplyRelation::create(['userid'=>$this->user['userId'],'r_food_id'=>$food_id]);
                    ReserveFoodPool::where('id',$food_id)->increment('likeCount',1);
                }
                return $this->successResponse('点赞成功');
            }elseif($status==2){//取消
                ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$food_id])->delete();
                if(ReserveFoodPool::where('id',$food_id)->value('likeCount')>0){
                    ReserveFoodPool::where('id',$food_id)->decrement('likeCount',1);
                }
                return $this->successResponse('取消点赞成功');
            }


        }
        return $this->response->error('点赞失败',$this->forbidden_code);
    }
}