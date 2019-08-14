<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/14
 * Time: 9:06 AM
 */
namespace App\Services;
use App\Model\Order;
use GuzzleHttp\Client;

class PaymentService{
    private $pay_unify_url='http://39.98.230.80:3020/api/pay/create_order';
    private $pay_type=['weixin' => 'WX_APP', 'allipay' => 'ALIPAY_MOBILE', 'card' => ''];
    public function pay(Order $order,$user,$pay_type){
        $client = new Client(['timeout'=>2.0]); //设置超时时间
        $data=[
            'mchId'=>"",
            'mchOrderNo'=>$order->order_sn,
            'channelId'=>$this->pay_type[$pay_type],
            'currency'=>'cny',
            'amount'=>$order->total_price * 100,
            'notifyUrl'=>$this->getNotifyUrl($pay_type),
            'subject'=>'订购外卖',
            'body'=>date('Y-m-d H:i:S').'订购外卖',
        ];
        $data['sign']=$this->getSign();
        $response = $client->post($this->pay_unify_url, [
            'form_params' => $data
        ]);
    }
    private function getSign($data,$key){

    }
    /**
     * 获取支付回调
     * @param $pay_type
     * @return string
     */
    private function getNotifyUrl($pay_type){
        if($pay_type=='weixin'){
           $route=route();
        }elseif ($pay_type=='allipay'){
            $route=route();
        }else{
            $route=route();
        }
        return $route;
    }
}