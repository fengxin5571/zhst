<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    protected $success_code          = 201; //请求成功状态码
    protected $unauth_code           = 401; //用户未授权（未登录、token失效、未注册）
    protected $forbidden_code        = 403; //请求无效，服务器拒绝执行（该状态码的错误信息需要提示给用户）
    protected $invalid_code          = 422; //请求信息存在语法错误（该状态码的错误信息需要提示给用户）
    protected $servererr_code        = 500; //服务器内部错误
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,Helpers;
    public function successResponse($message){
        return $this->response->array([
            'message'=>$message,
            'status_code'=>200
        ]);
    }
    public function withResponseToken($message='成功',$token){
        return $this->response->array([
            'message'=>$message,
            'status_code'=>200,
            'token'=>$token
        ]);
    }
}
