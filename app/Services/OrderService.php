<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/2
 * Time: 11:32 PM
 */
namespace App\Services;
use App\Model\Cart;
use App\Model\MarketFoodPool;
use App\Model\Order;
use App\Model\OrderFood;
use App\Model\ReserveFoodPool;
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
        $data['message']='订单插入成功';
        $data['error']=false;
        //开启一个事务
        DB::beginTransaction();
        try{
            //判断订单是否已经生成
            if(Order::where(['unique'=>$orderinfo['unique'],'userid'=>$user['userId']])->first()){
                throw new \Exception('请勿重复提交订单');
            }
            if(!array_key_exists($orderinfo['pay_type'],self::$payType)){
                throw new \Exception('支付方式有误');
            }

            if($orderinfo['order_type']==1){//如果是外卖订单
                //判断是否在下单时间段
                if(!(time()>strtotime(config('take_out_start'))&&time()<strtotime(config('take_out_end')))){
                    throw new \Exception('外卖订单已过下单时间段');
                }
                $orderinfo['get_time']=date('Y-m-d H:i:s');
            }elseif ($orderinfo['order_type']==3){//如果是网超
                $orderinfo['get_time']=date('Y-m-d H:i:s',strtotime("15:30")+24*60*60*2);
            }elseif($orderinfo['order_type']==2){//如果是网订
                $orderinfo['reserve_type']=3;
                $orderinfo['real_name']=$user['name'];
                $orderinfo['user_phone']=$user['phone'];
            }
            $order=new Order([
                'order_sn'=>Order::findAvailableNo(),
                'order_type'=>$orderinfo['order_type'],
                'userid'=>$user['userId'],
                'pay_type'=>$orderinfo['pay_type'],
                'real_name'=>$orderinfo['real_name'],
                'eat_people'=>isset($orderinfo['eat_people'])?$orderinfo['eat_people']:0,
                'user_phone'=>$orderinfo['user_phone'],
                'user_address'=>isset($orderinfo['user_address'])?$orderinfo['user_address']:'',
                'eat_time'=>isset($orderinfo['eat_time'])?$orderinfo['eat_time']:time(),
                'get_time'=>isset($orderinfo['get_time'])?$orderinfo['get_time']:time(),
                'mark'=>isset($orderinfo['mark'])?$orderinfo['mark']:'',
                'unique'=>$orderinfo['unique'],
                'reserve_type'=>isset($orderinfo['reserve_type'])?$orderinfo['reserve_type']:0,
            ]);
            $order->save();
            $totalAmount = 0;
            $totalNum=0;
            $box_charges=0;
            $carts_id=array_unique(explode(',',$orderinfo['carts_id']));
            $carts_id=array_filter($carts_id);
            if(!is_array($carts_id)||empty($carts_id)){
                throw new \Exception('参数错误');
            }
            foreach($carts_id as $cart_id){
                $cart=Cart::find($cart_id);
                if(!$cart){
                    throw new \Exception('购物车菜品已失效');
                }
                //如果为外卖
                if($orderinfo['order_type']==1){
                    if($cart->food_type==1){//如果是普通菜品
                        $food=TakeFoodPool::where('id',$cart->food_id)->first();
                        if($cart->cart_num>$food->stock){
                            throw new \Exception('菜品库存不足');
                        }
                        //餐盒费
                        $box_charges=bcadd($box_charges,bcmul($food->box_charge,$cart->cart_num,2),2);
                    }
                }elseif ($orderinfo['order_type']==3){//如果是网超
                    $food=MarketFoodPool::where('id',$cart->food_id)->first();
                    if($cart->cart_num>$food->stock){
                       // throw new \Exception('菜品库存不足');
                    }
                }else{//如果是网订
                    $food=ReserveFoodPool::where('id',$cart->food_id)->first();
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
            Cart::whereIn('id',$carts_id)->where(['userid'=>$user['userId'],'type'=>$orderinfo['order_type']])->delete();
            $data['order']=$order;
            DB::commit();
        }catch (\Exception $e){
            $data['error']=true;
            $data['message']=$e->getMessage();
            DB::rollback();
        }
        return $data;
    }
}