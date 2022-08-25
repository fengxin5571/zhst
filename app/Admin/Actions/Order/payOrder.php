<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/10/10
 * Time: 9:12 AM
 */
namespace App\Admin\Actions\Order;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
class payOrder extends RowAction{
    public $name = '支付订单';

    public function handle(Model $model)
    {
        // $model ...
        if($model->paid==0&&$model->status==0){
            $model->update(['paid'=>1]);

            return $this->response()->success('支付订单成功')->refresh();
        }
        return $this->response()->error('当前订单已不可支付');
    }
}