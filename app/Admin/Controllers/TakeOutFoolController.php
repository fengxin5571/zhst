<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/25
 * Time: 7:29 PM
 */
namespace App\Admin\Controllers;
use App\Model\TakeFoodPool;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class TakeOutFoolController extends AdminController{
    /**
     * 外卖菜品池列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('外卖菜品池')
               ->description('列表')
               ->breadcrumb(['text'=>'外卖菜品池'])
               ->body($this->grid());
    }

    /**
     * 新增菜品
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content
            ->header('新增菜品')
            ->description('新增')
            ->breadcrumb(['text'=>'新增菜品'])
            ->body($this->form());
    }
    protected function grid(){
        $grid=new Grid(new TakeFoodPool());
        return $grid;
    }
    protected function form(){
        $form=new Form(new TakeFoodPool());
        return $form;
    }
}