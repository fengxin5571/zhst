<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/23
 * Time: 10:40 AM
 */
namespace App\Admin\Extensions;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;
class ReserveFoodRelpyGender extends AbstractTool{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['gender' => '_gender_']);

        return <<<EOT

EOT;
    }

    public function render()
    {
        Admin::script($this->script());
        return view('admin.tools.gender1');
    }
}