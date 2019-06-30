<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/26
 * Time: 2:59 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\AdminMenu;
use Illuminate\Http\Request;

class testController extends Controller{
    public function index(Request $request){
            $menu=AdminMenu::find(1);
//            return $this->successResponse($menu);
//        return $this->response->error('',401);
    }
}