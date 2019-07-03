<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/2
 * Time: 11:32 PM
 */
namespace App\Services;
use App\Model\Cart;
use App\Model\Order;
use App\Model\TakeFoodPool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService{
    protected static $payType = ['weixin'=>'微信支付','allipay'=>'支付宝','card'=>'一卡通'];

    /**
     * 插入订单
     * @param $user
     * @param $orderinfo
     * @return mixed
     */
    public function store($user,$orderinfo){
        $data['message']='订单插入失败';
        $data['error']=false;
        //开启一个事务
        $data['order']=DB::transaction(function ()use($user,$orderinfo,&$data){
            $unique=str_random(30);
            //判断订单是否已经生成
            if(Order::where(['unique'=>$unique,'userid'=>$user['userId']])->first()){
                $data['error']=true;
                $data['message']='请勿重复提交订单';
                return;
            }
            if(!array_key_exists($orderinfo['pay_type'],self::$payType)){
                $data['error']=true;
                $data['message']='支付方式有误';
                return;
            }
            //$cartGroup=Cache::get('user_order_'.$user['userId']);
            $order=new Order([
                'order_sn'=>Order::findAvailableNo(),
                'order_type'=>$orderinfo['order_type'],
                'userid'=>$user['userId'],
                'pay_type'=>$orderinfo['pay_type'],
                'real_name'=>$orderinfo['real_name'],
                'user_phone'=>$orderinfo['user_phone'],
                'user_address'=>$orderinfo['user_address'],
                'eat_time'=>isset($orderinfo['eat_time'])?$orderinfo['eat_time']:time(),
                'get_time'=>isset($orderinfo['get_time'])?$orderinfo['get_time']:time(),
                'total_num'=>$orderinfo['total_num'],
                'total_price'=>$orderinfo['total_price'],
                'mark'=>isset($orderinfo['mark'])?$orderinfo['mark']:'',
                'unique'=>$unique
            ]);
            //$order->save();
            //dd($order);
            $carts_id=explode(',',$orderinfo['carts_id']);
            foreach($carts_id as $cart_id){
                $cart=Cart::find($cart_id);
                //如果为外卖
                if($orderinfo['order_type']==1){
                    if($cart->food_type==1){//如果是普通菜品
                        $food=TakeFoodPool::where('id',$cart->food_id)->first();
                    }
                }else{//如果是网订

                }
                $param= array(
                    'order_unique'=>$order->unique,
                    'food_name'   =>$food->name,
                    'food_image'  =>$food->food_image,
                    'food_price'  =>$food->price,
                    'food_type'   =>$food->
                );
            }
        });
        return $data;
    }
}