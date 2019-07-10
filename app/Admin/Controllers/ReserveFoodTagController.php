<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/10
 * Time: 8:42 AM
 */
namespace App\Admin\Controllers;
use App\Model\ReserveFoodTag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ReserveFoodTagController extends AdminController{
    /**
     * 菜品标签列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('网订菜品标签')
            ->description('列表')
            ->breadcrumb(['text' => '网订菜品标签'])
            ->body($this->grid());
    }

    /**
     * 新增网订菜品标签
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增菜品标签')
            ->description('新增')
            ->breadcrumb(['text' => '新增菜品标签'])
            ->body($this->form());
    }
    /**
     * 编辑网订菜品标签
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id,Content $content){
        return $content->header('编辑菜品标签')
            ->description('编辑')
            ->breadcrumb(['text' => '编辑菜品标签'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new ReserveFoodTag);
        $grid->column('id','ID')->sortable();
        $grid->column('r_tag_name','标签名称')->editable();
        return $grid;
    }
    protected function form()
    {
        $form=new Form(new ReserveFoodTag);
        $form->text('r_tag_name','标签名称')->required();
        return $form;
    }
}