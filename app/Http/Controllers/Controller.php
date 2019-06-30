<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
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
    protected $user;
    const LIMIT= 15;
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,Helpers;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user=$request->get('user');
            if(empty($this->user)){
                return $this->response->error('用户token验证失败',401);
            }
            return $next($request);
        });
    }

    public function successResponse($data,$message='success'){
        return $this->response->array([
            'message'=>$message,
            'status_code'=>200,
            'data'=>$data,
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
