<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/22
 * Time: 10:41 AM
 */
namespace App\Admin\Controllers;
use App\Admin\Actions\Order\payOrder;
use App\Admin\Extensions\SuccessButton;
use App\Model\Order;
use App\Model\ReserveFoodCategory;
use App\Model\ReserveOrder;
use App\Model\ReserveType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use App\Services\Common;
use Encore\Admin\Grid\Displayers\DropdownActions;
use App\Admin\Actions\Order\Replicate;
class ReserveOrderController extends AdminController{
    protected $pay_type=['weixin' => '微信支付', 'allipay' => '支付宝', 'card' => '一卡通'];
    /**
     * 订单列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('网订订单')
            ->description('列表')
            ->breadcrumb(['text' => '网订订单'])
            ->body($this->grid());
    }
    protected function grid(){
        $grid=new Grid(new Order());
        $grid->setActionClass(DropdownActions::class);
        $grid->model()->where('order_type',2);
        $grid->column('order_sn', '预定单号')->copyable();
        $grid->column('reserve_type','网订类型')->using(ReserveType::pluck('reserve_type_name','id')->toArray());
        $grid->column('订单菜品')->display(function (){
            if($this->order_type==2&&$this->reserve_type==3){
                $orderFoods = array_map(function ($food) {
                    return <<<EOT
             <div class="layui-table-cell laytable-cell-1-0-2">   
                 <p> 
                     <span> 
                        <img style="width: 30px;height: 30px;margin:0;cursor: pointer;" src="{$food['food_image']}"> 
                     </span> 
                     <span>{$food['food_name']}</span> 
                     <span> | ￥{$food['food_price']} × {$food['food_num']}</span>
                 </p>   
             </div>   
EOT;
                }, $this->orderFoods->toArray());

                return join('&nbsp;', $orderFoods);
            }
        });
        $grid->column('订餐人信息')->display(function(){
            return <<<EOT
             <div class="layui-table-cell laytable-cell-1-0-2">   
                 <p> 
                     姓名：<span class="label label-primary">{$this->real_name} </span> 
                 </p>   
                 <p> 
                     电话：<span class="label label-primary">{$this->user_phone}</span>
                 </p>   
             </div>   
EOT;

        });
        $grid->column('eat_people','就餐人数');
        $grid->column('就餐时间')->display(function (){
            $time='无就餐时间';
//            if($this->order_type==2&&$this->reserve_type==3){
//                $time=$this->get_time->toDateTimeString();
//            }
            if($this->order_type==2&&$this->reserve_type==1){
                $time='18:00-19:00';
            }elseif ($this->order_type==2&&$this->reserve_type==2||$this->order_type==2&&$this->reserve_type==3){
                $time='11:50-13:00';
            }
            return $time;
        });
        $grid->column('total_price', '订单总价')->style('text-align: center;')->display(function (){
            $price=$this->total_price;
            if($this->order_type==2&&$this->reserve_type==1){
                $price='此订单无支付';
            }
            return $price;
        });
        $grid->column('created_at','添加时间')->sortable();
        $grid->column('pay_type', '支付方式')->using($this->pay_type)->filter($this->pay_type);
        $grid->column('订单状态')->display(function(){
            return Common::get_r_reserve_order_status($this);
        });
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
            // append一个操作
           // $actions->prepend(new SuccessButton($actions->getKey()));
            //订单状态为未支付时可取消
            if($actions->row->paid==0&&$actions->row->status==0){
                $actions->add(new Replicate);
                if($actions->row->reserve_type==3){
                    $actions->add(new payOrder);
                }
            }

        });

        $grid->filter(function ($filter){
            $filter->column(1/2, function ($filter) {
                $filter->equal('order_type','订单类型')->select([''=>'所有','2'=>'网订']);
                $filter->equal('real_name','订餐人')->placeholder('请输入订餐人姓名查询');
                $filter->equal('user_phone','电话')->placeholder('请输入订餐人电话查询');

            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('reserve_type','网订类型')->select(ReserveType::pluck('reserve_type_name','id')->toArray());
                $filter->like('order_sn','订单号')->placeholder('请输入订单号查询');
                $filter->between('created_at','订单时间')->datetime();

            });
        });
        $grid->disableCreateButton();
        return $grid;
    }
    protected function form(){
        $form=new Form(new Order());
        return $form;
    }

    /**
     * 网订订单确认
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function success($id){
        if(!$id){
            return;
        }
        $data = ['status'  => true];
        $status=Order::find($id)->status;
        if($status==1){
            $data['message']= trans('请勿重复确认');
            admin_toastr('请勿重复确认', 'error',['timeOut'=>1000]);
        }elseif($status==-3){
            $data['message']= trans('此订单已取消');
            admin_toastr('此订单已取消', 'error',['timeOut'=>1000]);
        }else{
            Order::where('id',$id)->update(['status'=>1]);
            $data['message']= trans('已确认');
            admin_toastr('已确认', 'success',['timeOut'=>1000]);
        }
        return response()->json($data);
    }
}