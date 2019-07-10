<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/10
 * Time: 6:38 PM
 */
namespace App\Http\Controllers\Api;
use App\Model\PackageFood;
use App\Model\ReserveFoodPool;
use App\Model\ReserveType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class FoodsController extends Controller {

    public function index(Request $request){
        $food_type=$request->input('q');
        if($food_type==1){//普通菜品
            $data=ReserveFoodPool::get(['id',DB::raw('name as text')]);
        }else{//套餐
            $data=PackageFood::get(['id',DB::raw('package_name as text')]);
        }
        return $data;
    }
    public function type(Request $request){
        $reserveType=$request->input('q');
        $food_type=ReserveType::where('id',$reserveType)->value('reserve_type_seting');
        if($food_type==1){//普通菜品
            $data=ReserveFoodPool::where('is_show',1)->get(['id',DB::raw('name as text')]);
        }else{//套餐
            $data=PackageFood::where('is_show',1)->get(['id',DB::raw('package_name as text')]);
        }
        return $data;
    }
}