<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/24
 * Time: 6:11 PM
 */
namespace App\Services;
class Common{
    /**
     * 获取外卖订单状态
     * @param $order
     * @return string
     */
    public static function get_order_status($order){
        if($order->paid==0&&$order->status==0){
            $status_name = '未支付';
        }elseif($order->paid==0&&$order->status==-3){
            $status_name = '已取消';
        }elseif ($order->paid==1&&$order->status==0&&$order->refund_status==0){
            $status_name = '待发出';
        }elseif($order->paid==1&&$order->status==1&&$order->refund_status==0){
            $status_name = '已发出';
        }
        return $status_name;
    }
    /**
     * 获取网订单状态
     * @param $order
     * @return string
     */
    public static function get_r_order_status($order){
        if($order->status==0){
            $status_name='已预订';
        }elseif($order->status==-3){
            $status_name='已取消';
        }elseif ($order->status==1){
            $status_name='已确认';
        }
        return $status_name;
    }
}
