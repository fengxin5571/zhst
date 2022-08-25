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
use App\Model\MarketFoodPool;
use App\Model\Order;
use App\Model\ReserveFoodPool;
use App\Model\ReserveOrder;
use App\Model\ReserveType;
use App\Model\TakeFoodPool;
use App\Services\Common;
use function App\Services\get_order_status;
use App\Services\OrderService;
use App\Services\PaymentService;
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
                //订单类型1为外卖菜品；2为网上订餐；3为网超
                if($request->input('order_type')==1){
                    $cart_list->each(function ($item,$key)use($fail,$value){
                        //如果菜品类型普通菜品
                        if($item->food_type==1){
                            //不存在数据或已经下架
                            if(!$item->takeFood||!$item->takeFood->is_show){
                                $fail('购物车菜品失效');
                                return;
                            }
                        }else{//如果为套餐

                        }
                        if(strtotime($item->updated_at)+config('expire_time')*60<time()){
                            TakeFoodPool::where('id',$item->food_id)->update(['stock'=>$item->takeFood->stock+$item->cart_num]);
                            cart::destroy($item->id);
                            if(!Cart::whereIn('id',$value)->where('userid',$this->user['userId'])->get()->toArray()){
                                $fail('购物车菜品超时');
                                return;
                            }
                            //return;
                        }
                    });
                }elseif ($request->input('order_type')==3){//如果是网超
                    $cart_list->each(function ($item,$key)use($fail,$value){
                        //不存在数据或已经下架
                        if(!$item->marketFood||!$item->marketFood->is_show){
                            $fail('购物车菜品失效');
                            return;
                        }
                    });
                }else{//如果为网订
                    $cart_list->each(function ($item,$key)use($fail,$value){
                        //不存在数据或已经下架
                        if(!$item->reserveFood||!$item->reserveFood->is_show){
                            $fail('购物车菜品失效');
                            return;
                        }
                    });
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
            }elseif ($item->type==3){//如果是网超
                $marketFood=MarketFoodPool::where('id',$item->food_id)->first(['id','name','food_image','price']);
                //获取菜品信息
                $item->food_info=$marketFood;
                //计算订单价格
                $price_count=bcadd($price_count,bcmul($marketFood->price,$item->cart_num,2),2);

            }else{//如果是网订
                $reserveFood=ReserveFoodPool::where('id',$item->food_id)->first(['id','name','food_image','price']);
                //获取菜品信息
                $item->food_info=$reserveFood;
                //计算订单价格
                $price_count=bcadd($price_count,bcmul($reserveFood->price,$item->cart_num,2),2);
            }
        });
        $cart_list['order_type']=$request->input('order_type');
        $cart_list['cart_count']=Cart::where(['userid'=>$this->user['userId']])->whereIn('id',$carts_id)->sum('cart_num');
        $cart_list['box_charges']=$box_charges;
        $cart_list['price_count']=bcadd($price_count,$cart_list['box_charges'],2);
        if($request->input('order_type')==1){//订单为外卖时
            $cart_list['expire_time']=(config('expire_time')?:0).'分钟';
            $cart_list['create_order_time']=config('take_out_start').'-'.config('take_out_end');
            $cart_list['get_time']=config('get_time_start').'-'.config('get_time_end');
        }elseif ($request->input('order_type')==2){//订单为网订

        }elseif ($request->input('order_type')==3){//订单为网超
            $cart_list['get_time']=date('Y-m-d');
        }

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
//            'real_name.required'=>'订餐人姓名不能为空',
//            'user_phone.required'=>'订餐人电话不能为空',
//            'user_phone.is_mobile'=>'订餐人电话格式不正确',
//            'pay_type.required'=>'支付方式不能为空',
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
                    $fail('购物车数据不存在');
                    return;
                }
                //订单类型1为外卖菜品；2为网上订餐
                if($request->input('order_type')==1){
                    $cart_list->each(function ($item,$key)use($fail){
                        //如果菜品类型普通菜品
                        if($item->food_type==1){
                            //不存在数据或已经下架
                            if(!$item->takeFood||!$item->takeFood->is_show){
                                $fail('菜品不存在或已经下架');
                                return;
                            }
                        }else{//如果为套餐

                        }
                    });
                }else{//如果为网订

                }
            }],
//            'real_name'=>'required',
//            'user_phone'=>'required|is_mobile',
//            'pay_type'=>'required',
        ];
        //判断订单是外卖还是网订
        if($type==1){//外卖菜品
            $message=array_merge($message,[
                'user_address.required'=>'取餐地址不能为空',
                'real_name.required'=>'订餐人姓名不能为空',
                'user_phone.required'=>'订餐人电话不能为空',
                'user_phone.is_mobile'=>'订餐人电话格式不正确',
                'pay_type.required'=>'支付方式不能为空',
            ]);
            $rule=array_merge($rule,[
                'user_address'=>'required',
                'real_name'=>'required',
                'user_phone'=>'required|is_mobile',
                'pay_type'=>'required',
            ]);
        }elseif($type==3){//网超菜品
            $message=array_merge($message,[
                'user_address.required'=>'取餐地址不能为空',
                'real_name.required'=>'订餐人姓名不能为空',
                'user_phone.required'=>'订餐人电话不能为空',
                'user_phone.is_mobile'=>'订餐人电话格式不正确',
                'pay_type.required'=>'支付方式不能为空',
                //'get_time.required'=>'取货时间不能为空'
            ]);
            $rule=array_merge($rule,[
                'user_address'=>'required',
                'real_name'=>'required',
                'user_phone'=>'required|is_mobile',
                'pay_type'=>'required',
                //'get_time'=>'required'
            ]);
        }else{//网订菜品
            $message=array_merge($message,[
                'eat_people.required'=>'用餐人数不能为空',
                'eat_people.numeric' =>'用餐人数必须是数字',
                'pay_type.required'=>'支付方式不能为空',
            ]);
            $rule=array_merge($rule,[
                'eat_people'=>'required|numeric',
                'pay_type' =>'required',
            ]);
        }
        $validator=Validator::make($request->all(),$rule,$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $orderinfo=$request->all();
        try{
            $data=$this->orderService->store($this->user,$orderinfo);
        }catch (\Exception $e){
            $data['error']=true;
            $data['message']="提交订单失败";
        }
        if(!$data['error']){
            return $this->successResponse($data['order']);
        }
        return $this->response->error($data['message'],$this->forbidden_code);
    }

    /**
     * 网订加班餐、自助餐，自助点餐提交订单
     * @param Request $request
     * @return mixed
     */
    public function reserveAdd(Request $request){
        $type=$request->input('reserve_type',1);
        $message=[
            'unique.required'=>'唯一标识不能为空',
            'unique.unique'=>'请勿重复提交预定',
            'reserve_type.required'=>'网订类型id不能为空',
            'reserve_type.numeric'=>'网订类型id必须为数字',
//            'reserve_type.max'=>'不支持自助点餐下单'
        ];
        $rule=[
            'unique'=>'required|unique:order,unique',
            'reserve_type'=>'required|numeric',
        ];
        if($type==2){//自助餐预定
            $message=array_merge($message,[
                'eat_people.required'=>'用餐人数不能为空',
                'eat_people.numeric' =>'用餐人数必须是数字',
                'pay_type.required'=>'支付方式不能为空',
            ]);
            $rule=array_merge($rule,[
                'eat_people'=>'required|numeric',
                'pay_type' =>'required'
            ]);
        }elseif ($type==3){//自助点餐
            $message=array_merge($message,[
                'eat_people.required'=>'用餐人数不能为空',
                'eat_people.numeric' =>'用餐人数必须是数字',
                'pay_type.required'=>'支付方式不能为空',
                'get_time.required'=>'就餐时间不能为空',
                'carts_id.required'=>'购物车id不能为空',
            ]);
            $rule=array_merge($rule,[
                'eat_people'=>'required|numeric',
                'pay_type' =>'required',
                'get_time' =>'required',
                'carts_id' =>['required',function($attribute, $value, $fail) use($request){
                    if(!$value){
                        $fail('购物车id不能为空');
                        return;
                    }
                    $value=explode(',',$value);
                    $cart_list=Cart::whereIn('id',$value)->where('userid',$this->user['userId'])->get();
                    if(!$cart_list->toArray()){
                        $fail('购物车数据不存在');
                        return;
                    }
                    if($request->input('reserve_type')==3){
                        $cart_list->each(function ($item,$key)use($fail,$value){
                            //不存在数据或已经下架
                            if(!$item->reserveFood||!$item->reserveFood->is_show){
                                $fail('购物车菜品失效');
                                return;
                            }
                        });
                    }
                }]
            ]);
        }
        $validator=Validator::make($request->all(),$rule,$message);
        if($validator->fails()){
            return $this->response->error($validator->errors()->first(),$this->forbidden_code);
        }
        $sn=Order::findAvailableNo();
        $data=$request->all();
        $data['order_sn']=$sn;
        $data['order_type']=2;
        $data['userid']=$this->user['userId'];
        $data['real_name']=$this->user['name'];
        $data['user_phone']=$this->user['phone'];
        $data['eat_people']=$type==1?1:$request->input('eat_people');
        if($type==1){//网订加班餐
//            $data['total_price']=ReserveType::where('id',$type)->value('reserve_price');
        }elseif ($type==2){//自助餐预定
            $card_price=bcmul(1,config('card_convert'),2);
            $rserve_price=bcmul(ReserveType::where('id',$type)->value('reserve_price'),$data['eat_people']-1,2);
            $data['total_price']=bcadd($card_price,$rserve_price,2);
        }elseif ($type==3){//自助点餐
            if($data['get_time']==1){//上午
                $data['get_time']=date('Y-m-d H:i:s',strtotime(date("Y-m-d"))+60*60*12);
            }else{
                $data['get_time']=date('Y-m-d H:i:s',strtotime(date("Y-m-d"))+60*60*18);
            }
            //dd($data);
            try{
                $data=$this->orderService->store($this->user,$data);
            }catch (\Exception $e){
                $data['error']=true;
                $data['message']="提交订单失败";
            }
            if(!$data['error']){
                return $this->successResponse($data['order']);
            }
            return $this->response->error($data['message'],$this->forbidden_code);

        }
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
        }])->orderBy('created_at','desc')->get($fields);
        $data['order_list']->each(function ($item,$key)use($order_type){
            $item->status_name=$order_type==1?Common::get_order_status($item):Common::get_r_order_status($item);
            $item->reserve_info=ReserveType::where('id',$item->reserve_type)->first(['reserve_type_image','reserve_type_name']);
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
        $orderInfo->status_name=$orderInfo->order_type==1?Common::get_order_status($orderInfo):Common::get_r_order_status($orderInfo);
        $orderInfo->reserve_info=ReserveType::where('id',$orderInfo->reserve_type)->get(['reserve_type_name']);
        return $this->successResponse($orderInfo);
    }

    /**
     * 取消订单
     * @param Request $request
     * @return mixed
     */
    public function closeOrder(Request $request){
        $res=[
            'status'=>true,
            'message'=>'取消成功'
        ];
        $order_tppe=$request->input('order_type');
        $order_id=$request->input('order_id');
        $order=Order::where(['id'=>$order_id,'userid'=>$this->user['userId']])->first();
        if(!$order_tppe||!$order){
            return $this->response->error('参数错误或订单不存在',$this->forbidden_code);
        }
        if($order_tppe==1){//外卖订单
            if(!($order->paid==0&&$order->status==0)){
                $res['status']=false;
                $res['message']='当前订单已不可取消';
            }else{
                $order->update(['status'=>-3]);

            }
        }elseif ($order_tppe==2){//网订订单
            if($order->status==1){//订单为已确认
                $res['status']=false;
                $res['message']='当前订单已经确认，不能取消';
            }else{
                $order->update(['status'=>-3]);
            }
        }
        if(!$res['status']){
             return $this->response->error($res['message'],$this->forbidden_code);
        }
        return $this->successResponse('',$res['message']);
    }

    /**
     * 订单支付
     * @param Request $request
     * @return mixed
     */
    public function orderPay(Request $request){
        try{
            $order_id=$request->input('order_id');
            $order=Order::find($order_id);
            if(!$order_id||!$order){
                return $this->response->error('订单id我空或订单不存在',$this->forbidden_code);
            }
            if($order->paid){//订单已经支付
                return $this->response->error('订单已经支付',$this->forbidden_code);
            }
            if(!($order->paid==0&&$order->status==0)){//订单状态不是未支付
                return $this->response->error('订单状态不正确',$this->forbidden_code);
            }
            $pay=new PaymentService();
            if($order->pay_type=='card'){//一卡通支付
                $result=$pay->cardPay($order,$this->user,$order->pay_type);
            }else{//微信、支付宝支付
                $result=$pay->pay($order,$this->user,$order->pay_type);
            }
            if($result['error']){
                return $this->response->error($result['message'],$this->forbidden_code);
            }
            return $this->successResponse($result);
        }catch (\Exception $e){
                return $this->response->error($e->getMessage(),$this->forbidden_code);
        }

    }
}