<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/18
 * Time: 2:26 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Cart;
use App\Model\ReserveFoodCategory;
use App\Model\ReserveFoodPool;
use App\Model\ReserveFoodReply;
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
        $data['type_list']=ReserveType::get(['id','reserve_type_name','reserve_type_image','reserve_price']);
        $data['type_list']->each(function ($item,$key){
            if($item->id==1||$item->id==3){
                $item->reserve_price='此类型无价格';
            }
        });
        return $this->successResponse($data);
    }

    /**
     * 网订菜品分类
     * @return mixed
     */
    public function category(){
        $categoryList['category_list']=ReserveFoodCategory::get(['id','cat_name']);
        $cart_list=Cart::where(['userid'=>$this->user['userId'],'type'=>2,'food_type'=>1])->get();
        $categoryList['category_list']->each(function($item,$key) use($cart_list){
            $item->cart_num=0;
            foreach ($cart_list as $cart){
                if($cart->reserveFood->foodCategory->id==$item->id){
                    $item->cart_num=$item->cart_num+$cart->cart_num;
                }
            }
        });
        return $this->successResponse($categoryList);
    }
    /**
     * 网订菜品
     * @param Request $request
     * @return mixed
     */
    public function food(Request $request){
//       $message=[
//           'type_id.required'=>'类型id不能为空',
//           'type_id.numeric'=>'类型id需是数字'
//       ];
//        $validator=Validator::make($request->all(),[
//            'type_id'=>'required|numeric'
//        ],$message);
//        if($validator->fails()){
//            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
//        }
        //获取当天星期几
        $week=date('w');
        $fields=['id','cid','name','food_type','description','food_image','point','likeCount','price'];
//        $week_food_ids=ReserveMenu::where('reserve_type',$request->input('type_id'))->whereRaw("find_in_set('".$week."',weekly)")->pluck('food_id');
//        $data['food_list']=ReserveFoodCategory::get(['id','cat_name']);
//        $data['food_list']->each(function($item,$key)use ($week_food_ids,$fields){
//            $item['foods']=ReserveFoodPool::whereIn('id',$week_food_ids)->where(['is_show'=>1,'cid'=>$item->id])->get($fields);
//        });
        $data['food_list']=ReserveFoodCategory::get(['id','cat_name']);
        $data['food_list']->each(function($item,$key)use ($fields){
            $item['foods']=ReserveFoodPool::where(['is_show'=>1,'cid'=>$item->id])->get($fields);
        });
        return $this->successResponse($data);
    }

    /**
     * 今日菜谱领导
     * @param Request $request
     * @return mixed
     */
    public function todayLeader(Request $request){
        $today_type=$request->input('type',1);
        $cid=$request->input('cid');
        $data['food_list']=ReserveFoodPool::where('cid',$cid)->whereRaw("find_in_set('".$today_type."',is_today_leader)")->isShow()->get([
            'id','cid','name','price','description','cook','cook_image','cook_speciality','cook_group','food_image','point','likeCount'
            ]);
        $data['food_list']->each(function($item,$key){
            $item->is_like=ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$item->id,'type'=>'like'])->count()>0?true:false;
            $item->is_bad=ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$item->id,'type'=>'bad'])->count()>0?true:false;;
        });
        return $this->successResponse($data);
    }

    /**
     * 今日菜谱员工
     * @param Request $request
     * @return mixed
     */
    public function todayEmploy(Request $request){
        $today_type=$request->input('type',1);
        $cid=$request->input('cid');
        $data['food_list']=ReserveFoodPool::where('cid',$cid)->whereRaw("find_in_set('".$today_type."',is_today_employ)")->isShow()->get([
            'id','cid','name','price','description','cook','cook_image','cook_speciality','cook_group','food_image','point','likeCount'
        ]);
        $data['food_list']->each(function($item,$key){
            $item->is_like=ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$item->id,'type'=>'like'])->count()>0?true:false;
            $item->is_bad=ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$item->id,'type'=>'bad'])->count()>0?true:false;;
        });
        return $this->successResponse($data);
    }

    /**
     * 健康养生
     * @return mixed
     */
    public function health(){
        $data['food_list']=ReserveFoodPool::where('is_health',1)->isShow()->get(
            ['id','name','price','description','food_image']
        );
        return $this->successResponse($data);
    }

    /**
     * 养生详情
     * @param Request $request
     * @return mixed
     */
    public function shealthInfo(Request $request){
        $id=$request->input('id');
        $info=ReserveFoodPool::where('id',$id)->isShow()->first(
            ['id','name','price','description','food_image','health_images','health_content']
        );
        if(!$id||!$info){
            return $this->response->error('id为空或菜品不存在',$this->forbidden_code);
        }
        $info=$info->toArray();
        if(!$info['health_images']){
            $info['health_images']=$info['food_image'];
        }else{
            $info['health_images']=$info['health_images'][0];
        }
        return $this->successResponse($info);
    }

    /**
     * 厨艺交流
     * @return mixed
     */
    public function exchange(){
        $data['food_list']=ReserveFoodPool::where('is_exchange',1)->isShow()->get(
            ['id','name','price','description','food_image']
        );
        return $this->successResponse($data);
    }

    /**
     * 厨艺详情
     * @param Request $request
     * @return mixed
     */
    public function exchangeInfo(Request $request){
        $id=$request->input('id');
        $info=ReserveFoodPool::where('id',$id)->isShow()->first(
            ['id','name','price','description','food_image','ex_content','likeCount']
        );
        if(!$id||!$info){
            return $this->response->error('id为空或菜品不存在',$this->forbidden_code);
        }
        $info=$info->toArray();
        $info['is_like']=ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$info['id'],'type'=>'like'])->count()>0?true:false;
        return $this->successResponse($info);
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
                if(ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$food_id,'type'=>'like'])->count()==0){
                    ReserveFoodReplyRelation::create(['userid'=>$this->user['userId'],'r_food_id'=>$food_id,'type'=>'like']);
                    ReserveFoodPool::where('id',$food_id)->increment('likeCount',1);
                }
                return $this->successResponse('点赞成功');
            }elseif($status==2){//取消
                ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$food_id,'type'=>'like'])->delete();
                if(ReserveFoodPool::where('id',$food_id)->value('likeCount')>0){
                    ReserveFoodPool::where('id',$food_id)->decrement('likeCount',1);
                }
                return $this->successResponse('取消点赞成功');
            }


        }
        return $this->response->error('点赞失败',$this->forbidden_code);
    }
    /**
     * 网订菜品差评，取消
     * @param Request $request
     * @return mixed
     */
    public function bad(Request $request){
        $food_id=$request->get('food_id');
        $status=$request->get('status',1);
        if($food_id){
            if($status==1){//点赞
                if(ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$food_id,'type'=>'bad'])->count()==0){
                    ReserveFoodReplyRelation::create(['userid'=>$this->user['userId'],'r_food_id'=>$food_id,'type'=>'bad']);
//                    ReserveFoodPool::where('id',$food_id)->increment('likeCount',1);
                }
                return $this->successResponse('差评成功');
            }elseif($status==2){//取消
                ReserveFoodReplyRelation::where(['userid'=>$this->user['userId'],'r_food_id'=>$food_id,'type'=>'bad'])->delete();
//                if(ReserveFoodPool::where('id',$food_id)->value('likeCount')>0){
//                    ReserveFoodPool::where('id',$food_id)->decrement('likeCount',1);
//                }
                return $this->successResponse('取消差评成功');
            }


        }
        return $this->response->error('点赞失败',$this->forbidden_code);
    }
    /**
     * 网订评论列表
     * @param Request $request
     * @return mixed
     */
    public function comment(Request $request){
        $food_id=$request->input('food_id');
        $foodInfo=ReserveFoodPool::find($food_id);
        if(!$food_id||!$foodInfo){
            return $this->response->error('菜品id为空或菜品不存在',$this->forbidden_code);
        }
        $data['comment_list']=$foodInfo->comments()->orderBy('created_at','desc')->get(['id','food_id','avatar','reply_name','comment','created_at']);
        return $this->successResponse($data);
    }
    public function addComment(Request $request){
        $message=[
            'food_id.required'=>'网订菜品id不能为空',
            'comment.required'=>'评论内容不能为空',
            'food_id.numeric' =>'网订菜品id必须是数字'
        ];
        $validator=Validator::make($request->all(),[
            'food_id'=>'required|numeric',
            'comment'=>'required'
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $insert_array=[
            'food_id'=>$request->input('food_id'),
            'comment'=>$request->input('comment'),
            'avatar' =>'',
            'userid' =>$this->user['userId'],
            'reply_name'=>$this->user['name']
        ];
        if(!ReserveFoodReply::create($insert_array)){
            return $this->response->error('评论失败',$this->forbidden_code);
        }
        return $this->successResponse('','评论成功');
    }
}