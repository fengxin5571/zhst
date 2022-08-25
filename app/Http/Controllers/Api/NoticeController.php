<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/9/12
 * Time: 4:50 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller{
    /**
     * 公告列表
     * @param Request $request
     * @return mixed
     */
    public function notice(Request $request){
        $data['list']=Notice::orderBy('created_at','desc')->get(['id','title','content','created_at']);
        return $this->successResponse($data);
    }
}