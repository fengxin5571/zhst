<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/20
 * Time: 12:45 PM
 */
namespace App\Http\Controllers;
use App\Model\ReserveFoodPool;
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
            'lunch'=>''
        ];
        $foodList=ReserveFoodPool::whereIn('is_today',[0,1])->isShow()->orderBy('likeCount','desc')->get([
            'id','name','is_today']);
        $foodList->each(function($item,$key) use (&$data){
            if($item->is_today==0){
                $data['breakfast'].=$item->name.",";
            }elseif ($item->is_today==1){
                $data['lunch'].=$item->name.',';
            }
        });
        rtrim($data['breakfast'],',');
        rtrim($data['lunch'],',');
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