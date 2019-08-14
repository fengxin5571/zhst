<?php

namespace App\Admin\Actions\Order;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Replicate extends RowAction
{
    public $name = '取消订单';

    public function handle(Model $model)
    {
        // $model ...
        if($model->paid==0&&$model->status==0){
            $model->update(['status'=>-3]);
            return $this->response()->success('取消订单成功')->refresh();
        }
        return $this->response()->error('当前订单已不可取消');
    }

}