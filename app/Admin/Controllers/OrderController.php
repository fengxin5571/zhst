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
use Encore\Admin\Widgets\Table;

class OrderController extends AdminController
{
    protected static $status = [
        '1' => ['-1' => '申请退款', '0' => '待发出', '1' => '待取餐', '2' => '已取餐', '3' => '待评价', '-2' => '已退款'],
        '2' => [],
    ];

    public function index(Content $content)
    {
        return $content->header('订单管理')
            ->description('列表')
            ->breadcrumb(['text' => '订单管理'])
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Order());

        $grid->column('id', "ID");
        $grid->column('order_sn', '订单号');
        $grid->column('order_type', '类型')->using(['1' => '外卖', '2' => '网订']);
        $grid->column('total_num', '菜品总数');
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
        $grid->column('real_name', '订餐人');
        $grid->column('user_phone', '订餐人电话');
        $grid->column('total_price', '订单总价');
        $grid->column('paid', '支付状态')->using(['0' => '未支付', '2' => '已支付']);
        $grid->column('pay_type', '支付方式')->using(['weixin' => '微信支付', 'allipay' => '支付宝', 'card' => '一卡通']);
        $grid->column('status', '订单状态')->using(self::$status[1]);
        $grid->column('created_at', '订单时间');
        $grid->actions(function ($actions) {

        });
        $grid->disableCreateButton();
        return $grid;
    }
}