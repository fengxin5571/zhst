<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/22
 * Time: 10:41 AM
 */
namespace App\Admin\Controllers;
use App\Model\Order;
use App\Model\ReserveFoodCategory;
use App\Model\ReserveOrder;
use App\Model\ReserveType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ReserveOrderController extends AdminController{
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
        $grid->model()->where('order_type',2);
        $grid->column('id','ID')->sortable();
        $grid->column('order_type','订单类型')->using(['1'=>'外卖','2'=>'网订']);
        $grid->column('reserve_type','网订类型')->using(ReserveType::pluck('reserve_type_name','id')->toArray());
        $grid->column('order_sn', '预定单号')->copyable();
        $grid->column('real_name', '订餐人');
        $grid->column('user_phone', '订餐人电话');
        $grid->column('eat_people','就餐人数');
        $grid->column('eat_time','就餐时间');
        $grid->column('created_at','添加时间')->sortable();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
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
}