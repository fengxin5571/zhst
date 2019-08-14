<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/14
 * Time: 3:13 PM
 */
namespace App\Admin\Extensions;
use Encore\Admin\Admin;

class SuccessButton{
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    protected function script()
    {
        return <<<SCRIPT

        $('.grid-supplier-button').on('click', function () {
           
            // Your code.
            var id=$(this).data('id');
            $.ajax({
                method: 'get',
                url: '/admin/order/reserve/success/' + id,
                data: {
                    _method:'post',
                    _token:LA.token,
                },
                success: function (data) {
                    if(data.status){
                        $.pjax.reload('#pjax-container');
                    }
                    
                }
            });
            console.log($(this).data('id'));
        
        });

SCRIPT;
    }
    protected function render(){
        Admin::script($this->script());
        return "<a class='btn btn-xs btn-success fa fa-check  grid-supplier-button' style='margin-right: 5px;' data-id='{$this->id}'></a>";
    }
    public function __toString()
    {
        return $this->render();
    }
}