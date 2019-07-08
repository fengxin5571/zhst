<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/2
 * Time: 10:10 AM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Cart;
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
        $flog=$request->get('flog','+');
        $message=[
            'food_id.required'=>'请选择菜品',
            'num.required'=>'数量不能为零',
            'type.required'=>'添加类型不能为空',
            'food_type.required'=>'菜品类型不能为空'
        ];
        $validator=Validator::make($request->all(),[
            'food_type'=>'required',
            'type'=>'required',
            'food_id'=>['required',function($attribute, $value, $fail)use($request){
                //type为1为外卖菜品2为网上订购菜品
                $foodsku=TakeFoodPool::class;
                //如果是外卖菜品
                if($request->input('type')==1){
                    //如果是普通商品
                    if($request->input('food_type')==1){
                        $foodsku=TakeFoodPool::class;
                    }else{//如果是套餐
                        $foodsku='';
                    }
                }else{//如果是网订
                    $foodsku='';
                }
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
        if($item=$this->cartService
            ->add($request->input('food_id'),$request->input('num'),$request->input('type'),$request->input('food_type'),$this->user,$flog)){
            $data['cart_count']=Cart::where(['userid'=>$this->user['userId'],'type'=>$request->input('type')])->sum('cart_num');
            $data['food_info']=$item;
            return $this->successResponse($data,'成功');
        }
        return $this->response->error('失败',$this->forbidden_code);
    }
    /**
     * 查看购物车
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request){
        $type=$request->input('type',1);
        //$food=$type==1?'takeFood':'';
        $cart_count=0;$price_count=0;
        $cart=Cart::where(['userid'=>$this->user['userId'],'type'=>$type]);
        $cartList=Cart::where(['userid'=>$this->user['userId'],'type'=>$type])->get(['id','food_id','cart_num','food_type','created_at']);
        $data['food_list']=Cart::where(['userid'=>$this->user['userId'],'type'=>$type]);
        $cartList->each(function ($item,$key) use($type,$cart){
            //type为1为外卖菜品2为网上订购菜品
            if($type==1){
                //如果为普通商品
                if($item->food_type==1){
                    $item['food_list']=$cart->with('takeFood:id,name,food_image,price,description');
                }

            }else{

            }
        });
        $data['food_list']=$cart->get(['id','food_id','cart_num','food_type','created_at']);
//        $item['food_list']=Cart::where(['userid'=>$this->user['userId'],'type'=>$type])
//            ->with("{$food}:id,name,food_image,price")->get(['id','food_id','cart_num','created_at']);
        $data['food_list']->each(function($item,$key) use(&$cart_count,&$price_count){
            $item->foodCountPrice=bcmul($item->takeFood->price,$item->cart_num,2);
            $cart_count+=$item->cart_num;
            $price_count+=$item->foodCountPrice;
        });
        $data['cart_count']=$cart_count;
        $data['price_count']=number_format($price_count,2);
        return $this->successResponse($data);
    }

    /**
     *
     * 查看购物车总量
     * @param Request $request
     * @return mixed
     */
    public function Cartnum(Request $request){
        $type=$request->input('type',1);
        $food=$type==1?'takeFood':'';$price_count=0;
        $data['cart_count']=Cart::where(['userid'=>$this->user['userId'],'type'=>$type])->sum('cart_num');
        Cart::where(['userid'=>$this->user['userId'],'type'=>$type])->with("{$food}")->get()
            ->each(function($item,$key) use(&$price_count){
                $price_count+=bcmul($item->takeFood->price,$item->cart_num,2);
            });
        $data['price_count']=number_format($price_count,2);
        return $this->successResponse($data);
    }

    /**
     * 清空购物车
     * @param Request $request
     * @return mixed
     */
    public function remove(Request $request)
    {
        $type=$request->input('type',1);
        $this->cartService->remove($type,$this->user);
        return $this->successResponse('','成功');

    }
}