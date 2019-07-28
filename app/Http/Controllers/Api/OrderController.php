<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/2
 * Time: 10:43 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Cart;
use App\Model\Order;
use App\Model\ReserveOrder;
use App\Model\ReserveType;
use App\Model\TakeFoodPool;
use App\Services\Common;
use function App\Services\get_order_status;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderController extends Controller{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService=$orderService;
    }
    /**
     * 结算中心
     * @param Request $request
     * @return mixed
     */
    public function confirmOrder(Request $request){
        $message=[
            'order_type.required'=>'订单类型不能为空',
            'carts_id.required'=>'购物车id不能为空',
        ];
        $validator=Validator::make($request->all(),[
            'order_type'=>'required',
            'carts_id'=>['required',function($attribute, $value, $fail) use($request){
                if(!$value){
                    $fail('购物车id不能为空');
                    return;
                }
                $value=explode(',',$value);
                $cart_list=Cart::whereIn('id',$value)->where('userid',$this->user['userId'])->get();
                if(!$cart_list->toArray()){
                    $fail('购物车菜品失效');
                    return;
                }
                //订单类型1为外卖菜品；2为网上订餐
                if($request->input('order_type')==1){
                    $cart_list->each(function ($item,$key)use($fail){
                        //如果菜品类型普通菜品
                        if($item->food_type==1){
                            //不存在数据或已经下架
                            if(!$item->takeFood||!$item->takeFood->is_show){
                                $fail('购物车菜品失效');
                                return;
                            }
                        }else{//如果为套餐

                        }
                    });
                }else{//如果为网订

                }
            }]
        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $carts_id=explode(',',$request->input('carts_id'));
        $price_count=0;$box_charges=0;
        $cart_list['cart_list']=Cart::where(['userid'=>$this->user['userId']])->whereIn('id',$carts_id)->get(['id','type','food_id','food_type','cart_num','created_at']);
        $cart_list['cart_list']->each(function($item,$key)use(&$price_count,&$box_charges){
            if($item->type==1){//如果是外卖菜品
                if($item->food_type==1){//如果是普通菜品
                    $takeFood=TakeFoodPool::where('id',$item->food_id)->first(['id','name','food_image','price','box_charge']);
                    //获取菜品信息
                    $item->food_info=$takeFood;
                    //计算订单价格
                    $price_count=bcadd($price_count,bcmul($takeFood->price,$item->cart_num,2),2);
                    //计算订单餐盒费
                    $box_charges=bcadd($box_charges,bcmul($takeFood->box_charge,$item->cart_num,2),2);
                }else{

                }
            }else{//如果是网订

            }
        });
        $cart_list['order_type']=$request->input('order_type');
        $cart_list['cart_count']=Cart::where(['userid'=>$this->user['userId']])->whereIn('id',$carts_id)->sum('cart_num');
        $cart_list['box_charges']=$box_charges;
        $cart_list['price_count']=bcadd($price_count,$cart_list['box_charges'],2);
//        $userid=$this->user['userId'];
        //Cache::put('user_order_'.$this->user['userId'],compact('carts_id','userid'),10);
        return $this->successResponse($cart_list);
    }
    /**
     * 创建订单
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request){
        $type=$request->input('order_type');
        $message=[
            'order_type.required'=>'订单类型不能为空',
            'unique.required'=>'唯一标识不能为空',
            'real_name.required'=>'订餐人姓名不能为空',
            'user_phone.required'=>'订餐人电话不能为空',
            'user_phone.is_mobile'=>'订餐人电话格式不正确',
            'pay_type.required'=>'支付方式不能为空',
        ];
        $rule=[
            'order_type'=>'required',
            'unique'=>'required',
            'carts_id'=>['required',function($attribute, $value, $fail) use($request){
                if(!$value){
                    $fail('购物车id不能为空');
                    return;
                }
                $value=explode(',',$value);
                $cart_list=Cart::whereIn('id',$value)->where('userid',$this->user['userId'])->get();
                if(!$cart_list->toArray()){
                    $fail('购物车菜品失效');
                    return;
                }
                //订单类型1为外卖菜品；2为网上订餐
                if($request->input('order_type')==1){
                    $cart_list->each(function ($item,$key)use($fail){
                        //如果菜品类型普通菜品
                        if($item->food_type==1){
                            //不存在数据或已经下架
                            if(!$item->takeFood||!$item->takeFood->is_show){
                                $fail('购物车菜品失效');
                                return;
                            }
                        }else{//如果为套餐

                        }
                    });
                }else{//如果为网订

                }
            }],
            'real_name'=>'required',
            'user_phone'=>'required|is_mobile',
            'pay_type'=>'required',
        ];
        //判断订单是外卖还是网订
        if($type==1){
            $message=array_merge($message,[
                'user_address.required'=>'取餐地址不能为空',
                'get_time.required'=>'取餐时间不能为空',
            ]);
            $rule=array_merge($rule,[
                'user_address'=>'required',
                'get_time' =>'required',
            ]);
        }else{
            $message=array_merge($message,[
                'eat_time.required'=>'就餐时间不能为空',
            ]);
            $rule=array_merge($rule,[
                'eat_time'=>'required'
            ]);
        }
        $validator=Validator::make($request->all(),$rule,$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $orderinfo=$request->all();
        $data=$this->orderService->store($this->user,$orderinfo);
        if(!$data['error']){
            return $this->successResponse($data['order']);
        }
        return $this->response->error($data['message'],$this->forbidden_code);
    }

    /**
     * 提交网订预定单
     * @param Request $request
     * @return mixed
     */
    public function reserveAdd(Request $request){
        $sn=Order::findAvailableNo();
        $message=[
            'unique.required'=>'唯一标识不能为空',
            'unique.unique'=>'请勿重复提交预定',
            'real_name.required'=>'订餐人姓名不能为空',
            'user_phone.required'=>'订餐人电话不能为空',
            'user_phone.is_mobile'=>'订餐人电话格式不正确',
            'eat_people.required'=>'就餐人数不能为空',
            'eat_people.numeric'=>'就餐人数必须为数字',
            'eat_people.gt'=>'就餐人数必须大于0',
            'eat_time.required'=>'就餐时间不能为空',
            'reserve_type.required'=>'网订类型id不能为空',
            'reserve_type.numeric'=>'网订类型id必须为数字',
        ];
        $validator=Validator::make($request->all(),[
            'unique'=>'required|unique:order,unique',
            'real_name'=>'required',
            'user_phone'=>'required|is_mobile',
            'eat_people'=>'required|numeric|gt:0',
            'eat_time'  =>'required',
            'reserve_type'=>'required|numeric'

        ],$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $data=$request->all();
        $data['order_sn']=$sn;
        $data['order_type']=2;
        $data['userid']=$this->user['userId'];
        if(!$order=Order::create($data)){
            return $this->response->error('预定失败',$this->forbidden_code);
        }
        $order->reserve_name=ReserveType::where('id',$order->reserve_type)->value('reserve_type_name');
        return $this->successResponse($order);
    }

    /**
     * 我的订单
     * @param Request $request
     * @return mixed
     */
    public function myOrder(Request $request){
        $order_type=$request->input('order_type',1);
        $fields=['id','order_sn','order_type','unique','created_at','reserve_type','paid','refund_status','status'];
        $data['order_list']=Order::where(['order_type'=>$order_type,'userid'=>$this->user['userId']])->with(['orderFoods'=>function($query){
            $query->select('id','order_unique','food_name','food_price','food_image','food_num');
        }])->get($fields);
        $data['order_list']->each(function ($item,$key)use($order_type){
            $item->status_name=$order_type==1?Common::get_order_status($item):'';
            $item->reserve_info=ReserveType::where('id',$item->reserve_type)->get(['reserve_type_image','reserve_type_name']);
        });
        return $this->successResponse($data);
    }

    /**
     * 订单详情
     * @param Request $request
     * @return mixed
     */
    public function orderDetails(Request $request){
        $order_id=$request->input('order_id');
        $orderInfo=Order::with(['orderFoods'=>function($query){
            $query->select('id','order_unique','food_name','food_price','food_image','food_num');
        }])->find($order_id);
        if(!$order_id||!$orderInfo){
            return $this->response->error('订单获取失败，id为空或订单不存在',$this->forbidden_code);
        }
        $orderInfo->status_name=$orderInfo->order_type==1?Common::get_order_status($orderInfo):'';
        $orderInfo->reserve_info=ReserveType::where('id',$orderInfo->reserve_type)->get(['reserve_type_name']);
        return $this->successResponse($orderInfo);
    }
}