<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/24
 * Time: 6:11 PM
 */
namespace App\Services;
class Common{
    public static function get_order_status($order){
        if($order->paid==0&&$order->status==0){
            $status_name='未支付';
        }elseif($order->paid==0&&$order->status==-3){
            $status_name='已取消';
        }elseif ($order->paid==1&&$order->status==0&&$order->refund_status==0){
            $status_name='待发出';
        }
        return $status_name;
    }
}
