<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/2
 * Time: 4:35 PM
 */

namespace App\Admin\Controllers;

use App\Model\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;

class OrderController extends AdminController
{


    /**
     * 订单列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content->header('订单管理')
            ->description('列表')
            ->breadcrumb(['text' => '订单管理'])
            ->body($this->grid());
    }

    /**
     * 查看订单
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id,Content $content){
        $info=Order::find($id);
        return $content->header('订单详情')
            ->description('详细')
            ->breadcrumb(['text' => '订单详情'])
            ->body(Admin::show($info,function($show){
                $show->order_sn('订单号：');
                $show->order_type('订单类型：')->using(['1'=>'外卖','2'=>'网订']);
                $show->orderFoods('订单菜品')->unescape()->as(function ($orderFoods){
                    $html='';
                    foreach ($orderFoods as $food){
                        $html.= <<<EOT
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
                    }
                    return $html;
                });
                $show->total_num('菜品总数：');
                $show->real_name('订餐人：');
                $show->user_phone('订餐人电话：');
                $show->box_charges('餐盒费：')->as(function ($box_charges){
                    return '￥'.$box_charges;
                });
                $show->total_price('订单总价：')->as(function ($total_price){
                    return '￥'.$total_price;
                });

            }));
    }
    protected function grid()
    {
        $grid = new Grid(new Order());
        $grid->model()->where('order_type',1);
        $grid->column('order_sn', '订单号')->copyable();
        $grid->orderFoods('菜品信息')->display(function ($orderFoods) {
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
            }, $orderFoods);

            return join('&nbsp;', $orderFoods);
        });
        $grid->column('total_num', '菜品总数')->style('text-align: center;');
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
        $grid->column('box_charges','餐盒费');
        $grid->column('total_price', '订单总价')->style('text-align: center;');
        $grid->column('paid', '支付状态')->using(['0' => '未支付', '1' => '已支付']);
        $grid->column('pay_type', '支付方式')->using(['weixin' => '微信支付', 'allipay' => '支付宝', 'card' => '一卡通']);
        $grid->column('订单状态')->display(function(){
            return self::get_order_status($this);
        });
        $grid->column('created_at', '订单时间')->sortable();

        $grid->filter(function ($filter){
            $filter->column(1/2, function ($filter) {
                $filter->equal('order_type','订单类型')->select([''=>'所有','1'=>'外卖']);
                $filter->equal('real_name','订餐人')->placeholder('请输入订餐人姓名查询');
                $filter->equal('user_phone','电话')->placeholder('请输入订餐人电话查询');
                $filter->equal('pay_type','支付方式')->radio([
                    'weixin'   => ' 微信',
                    'allipay'  => ' 支付宝',
                    'card'     => ' 一卡通',
                ]);
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('order_sn','订单号')->placeholder('请输入订单号查询');
                $filter->between('created_at','订单时间')->datetime();
                $filter->between('total_price','订单总价');
                $filter->equal('paid','支付状态')->radio([
                    ''   => ' 所有',
                    0    => ' 未支付',
                    1    => ' 已支付',
                ]);

            });
        });
        $grid->actions(function ($actions) {
//
        });
        $grid->disableCreateButton();
        return $grid;
    }

    /**
     * 获取订单状态
     * @param $order
     * @return string
     */
    protected static function get_order_status($order){
        if($order->paid==0&&$order->status==0){
            $status_name='未支付';
        }elseif ($order->paid==1&&$order->status==0&&$order->refund_status==0){
            $status_name='待发出';
        }
        return $status_name;
    }
}