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
use App\Model\OrderFood;
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
            //判断订单是否已经生成
            if(Order::where(['unique'=>$orderinfo['unique'],'userid'=>$user['userId']])->first()){
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
                'mark'=>isset($orderinfo['mark'])?$orderinfo['mark']:'',
                'unique'=>$orderinfo['unique']
            ]);
            $order->save();
            $totalAmount = 0;
            $totalNum=0;
            $box_charges=0;
            $carts_id=array_unique(explode(',',$orderinfo['carts_id']));
            $carts_id=array_filter($carts_id);
            if(!is_array($carts_id)||empty($carts_id)){
                $data['error']=true;
                $data['message']='参数错误';
                return;
            }
            foreach($carts_id as $cart_id){
                $cart=Cart::find($cart_id);
                if(!$cart){
                    $data['error']=true;
                    $data['message']='购物车菜品已失效';
                    return;
                }
                //如果为外卖
                if($orderinfo['order_type']==1){
                    if($cart->food_type==1){//如果是普通菜品
                        $food=TakeFoodPool::where('id',$cart->food_id)->first();
                        //餐盒费
                        $box_charges=bcadd($box_charges,bcmul($food->box_charge,$cart->cart_num,2),2);
                    }
                }else{//如果是网订

                }
                //订单总价
                $totalAmount =bcadd($totalAmount,bcmul($food->price,$cart->cart_num,2),2);
                $totalNum +=$cart->cart_num;
                $param= array(
                    'order_unique'=>$order->unique,
                    'food_name'   =>$food->name,
                    'food_image'  =>$food->food_image,
                    'food_price'  =>$food->price,
                    'food_type'   =>$food->food_type,
                    'food_num'    =>$cart->cart_num,
                );
                $orderFood=OrderFood::create($param);
            }
            //更新订单总价与菜品总量
            $order->update([
                'total_price'=>bcadd($totalAmount,$box_charges,2),
                'total_num'=>$totalNum,
                'box_charges'=>$box_charges
            ]);
            Cart::whereIn('id',$carts_id)->where('userid',$user['userId'])->delete();
            return $order;
        });
        return $data;
    }
}