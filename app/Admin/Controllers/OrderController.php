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
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class OrderController extends AdminController{
    public function index(Content $content){
        return $content->header('订单管理')
            ->description('列表')
            ->breadcrumb(['text'=>'订单管理'])
            ->body($this->grid());
    }
    protected function grid(){
        $grid=new Grid(new Order());
        $grid->column('id',"ID");
        $grid->column('order_sn','订单号');
        $grid->column('order_type','类型')->using(['1'=>'外卖','2'=>'网订']);
        $grid->column('real_name','订餐人');
        $grid->column('user_phone','订餐人电话');
        $grid->column('total_price','订单总价');
        $grid->column('paid','支付状态')->using(['0'=>'未支付','2'=>'已支付']);
        $grid->column('pay_type','支付方式');
        $grid->column('status','订单状态');
        $grid->column('created_at','订单时间');
        $grid->disableCreateButton();
        return $grid;
    }
}