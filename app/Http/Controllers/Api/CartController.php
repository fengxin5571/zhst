<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/2
 * Time: 10:10 AM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\TakeFoodPool;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        parent::__construct();
        $this->cartService=$cartService;
    }

    /**
     * 添加到购物车
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request){
        $message=[
            'food_id.required'=>'请选择菜品',
            'num.required'=>'数量不能为零',
            'type.required'=>'添加类型不能为空',
        ];
        $validator=Validator::make($request->all(),[
            'type'=>'required',
            'food_id'=>['required',function($attribute, $value, $fail)use($request){
                //type为1为外卖菜品2为网上订购菜品
                $foodsku=$request->input('type')==1?TakeFoodPool::class:'';
                if(!$food=$foodsku::find($value)){
                    $fail('该菜品不存在');
                    return;
                }
                if (!$food->is_show) {
                    $fail('该菜品未上架');
                    return ;
                }
            }],
            'num'=>'required|min:1',
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        if($this->cartService->add($request->input('food_id'),$request->input('num'))){
            return $this->successResponse('','添加购物车成功');
        }
        return $this->response->error('添加购物车失败',$this->forbidden_code);
    }
    public function index(){

    }
}