<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/29
 * Time: 10:32 AM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class UserController extends Controller{
    public function my(){
        $data['userinfo']=$this->user;
        return $this->successResponse($data);
    }
}