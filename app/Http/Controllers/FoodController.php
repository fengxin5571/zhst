<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/20
 * Time: 12:45 PM
 */
namespace App\Http\Controllers;
use App\Model\ReserveFoodPool;
use App\Model\TakeFoodPool;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
class FoodController extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,Helpers;

    /**
     * app首页今日菜谱
     * @return mixed
     */
    public function todayFood(){
        $data=[
            'breakfast'=>'',
            'lunch'=>'',
            'takeout'=>'',
        ];
        $foodList=ReserveFoodPool::isShow()->orderBy('likeCount','desc')->get([
            'id','name','is_recommend']);
        $count=$num=0;

        $foodList->each(function($item,$key) use (&$data,&$count,&$num){
            if(in_array(1,$item->is_recommend)){
                if($count<3){
                    $data['breakfast'].=$item->name.",";
                }
                $count++;
            }
            if (in_array(2,$item->is_recommend)){
                if($num<3){
                    $data['lunch'].=$item->name.',';
                }
                $num++;
            }

        });
        $takefood=TakeFoodPool::isShow()->where('is_recommend',1)->get(['id','name']);
        $takefood->each(function($item,$key) use(&$data){
            if($key<3){
                $data['takeout'].=$item->name.',';
            }else{
                $data['takeout']=rtrim($data['takeout'],',');
                $data['takeout'].='...';
                return false;
            }

        });
        $data['breakfast']=rtrim($data['breakfast'],',');
        if(substr_count($data['breakfast'],',')==2){
            $data['breakfast'].='...';
        }
        $data['lunch']=rtrim($data['lunch'],',');
        if(substr_count($data['lunch'],',')==2){
            $data['lunch'].='...';
        }
        $data['takeout']=rtrim($data['takeout'],',');

        return $this->successResponse($data);
    }
    public function successResponse($data,$message='success'){
        return $this->response->array([
            'message'=>$message,
            'status_code'=>200,
            'data'=>$data,
        ]);
    }
}