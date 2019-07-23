<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/23
 * Time: 8:45 AM
 */
namespace App\Admin\Extensions;
use Encore\Admin\Admin;
class ResrveFoodRelpy{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    protected function script()
    {
        return <<<SCRIPT

        

SCRIPT;
    }
    protected function render(){
        Admin::script($this->script());
        return "<a class='reply style='margin-right: 5px;' data-id='{$this->id}' href='reservePool/comment?&food_id={$this->id}'><i class='fa fa-commenting' title='查看评论'></i></a>";
    }
    public function __toString()
    {
        return $this->render();
    }
}