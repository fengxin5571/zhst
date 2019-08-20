<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/14
 * Time: 8:25 PM
 */
namespace App\Http\Controllers;
use App\Services\PaymentService;
use Illuminate\Routing\Controller as BaseController;
class  PaymentController extends BaseController{
    /**
     * 支付回调
     * @return array
     */
    public function payNotify(){
        $result=PaymentService::wechatNotify();
        return $result;
    }
}