<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/10
 * Time: 9:21 PM
 */
namespace App\Admin\Extensions;
use App\Model\ReserveType;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class ReserveMenuGender extends AbstractTool{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['gender' => '_gender_']);

        return <<<EOT

$('input:radio.user-gender').change(function () {

    var url = "$url".replace('_gender_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());
//        $options=ReserveType::all()->pluck('reserve_type_name','id');
        $options=['1'=>'早餐','2'=>'午餐','3'=>'加班餐'];
        return view('admin.tools.gender', compact('options'));
    }
}