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
    private $pay_type=['weixin' => 'WX_APP', 'allipay' => 'ALIPAY_MOBILE', 'card' => 'card'];
    private $subject=['1'=>'订购外卖','2'=>'订购网订','3'=>'订购网超'];
    private $key='M86l522AV6q613Ii4W6u8K48uW8vM1N6bFgyv769220MdYe9u37N4y7rI5mQ'; //签名秘钥
    private $repKey='';//验签秘钥

    /**
     * 微信、支付宝
     * @param Order $order
     * @param $user
     * @param $pay_type
     * @return array
     */
    public function pay(Order $order,$user,$pay_type){
        $res=[
            'data'=>[],
            'error'=>false,
            'message'=>'订单支付成功'
        ];

        $client = new Client(['timeout'=>2.0]); //设置超时时间
        $data=[
            'mchId'=>"10000000",
            'mchOrderNo'=>$order->order_sn,
            'channelId'=>$this->pay_type[$pay_type],
            'currency'=>'cny',
            'amount'=>$order->total_price * 100,
            'clientIp'=>'0.0.0.0',
            'device' =>'Andriod',
            'notifyUrl'=>route('payment.notify'),
            'subject'=>$this->subject[$order->order_type],
            'body'=>$this->subject[$order->order_type],
        ];
        //生成签名
        $data['sign']=$this->getSign($data,$this->key);
        $response = $client->post($this->pay_unify_url, [
            'form_params' => ['params'=>json_encode($data)]
        ]);
        $result=json_decode($response->getBody()->getContents(),false);
        if($result->retCode=='FAIL'){
            $res['error']=true;
            $res['message']=$result->retMsg;
        }elseif ($result->retCode=='SUCCESS'){
            $res['data']=$result->payParams;
        }
        return $res;
    }

    /**
     * 一卡通
     * @param Order $order
     * @param $user
     * @param $pay_type
     * @return array
     */
    public function cardPay(Order $order,$user,$pay_type){
        $res=[
            'data'=>[],
            'error'=>false,
            'message'=>'订单支付成功'
        ];
        return $res;
    }
    /**
     * 签名生成
     * @param $data
     * @param $key
     * @return string
     */
    private function getSign($data,$key){
        ksort($data);
        //数组转成URL键值对并拼接商户key
        $str=urldecode( http_build_query($data)."&key=".$key);
        //MD5加密并转成大写
        return strtoupper(md5($str));

    }
    /**
     * 验证签名
     * @param $data
     * @param $key
     * @return string
     */
    public function checkSign($data,$key){
        return '';
    }
    /**
     * 微信回调
     * @return array
     */
    public static function wechatNotify(){
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($postObj === false) die('parse xml error');
        if ($postObj->return_code != 'SUCCESS') die($postObj->return_msg);
        if ($postObj->result_code != 'SUCCESS') die($postObj->err_code);
        $arr = (array)$postObj;
        $order=Order::where(['order_sn'=>$arr['out_trade_no']])->first();
        if (!$order) {
            return 'fail';
        }
        // 订单已支付
        if ($order->paid) {
            // 告知微信支付此订单已处理
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }
    public static function alipayNotify(){

    }
}