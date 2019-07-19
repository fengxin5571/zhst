<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/19
 * Time: 4:12 PM
 */
namespace App\Admin\Extensions;
use Encore\Admin\Grid\Tools\BatchAction;

class ReserveFoodToday extends BatchAction{
    protected $type;

    public function __construct($type = 0)
    {
        $this->type = $type;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: '{$this->resource}/today',
        data: {
            _token:LA.token,
            ids:  $.admin.grid.selected(),
            type: {$this->type}
        },
        success: function () {
            $.pjax.reload('#pjax-container');
            toastr.success('操作成功');
        }
    });
});

EOT;
    }

}