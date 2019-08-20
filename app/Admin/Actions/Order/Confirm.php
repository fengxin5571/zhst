<?php

namespace App\Admin\Actions\Order;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Confirm extends RowAction
{
    public $name = '订单发货';

    public function handle(Model $model)
    {
        // $model ...
        if($model->paid==1&&$model->status==0&&$model->refund_status==0){
            $model->update(['status'=>1]);
            return $this->response()->success('订单发货成功')->refresh();
        }
        return $this->response()->error('当前订单不可发货');
    }

}