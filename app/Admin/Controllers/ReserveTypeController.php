<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/9
 * Time: 8:32 AM
 */
namespace App\Admin\Controllers;
use App\Model\ReserveType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ReserveTypeController extends AdminController{
    /**
     * 网订类型管理
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('网订类型')
            ->description('列表')
            ->breadcrumb(['text' => '网订类型'])
            ->body($this->grid());
    }

    /**
     * 网订类型编辑
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id,Content $content){
        return $content->header('编辑类型')
            ->description('编辑')
            ->breadcrumb(['text' => '编辑类型'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new ReserveType());
        $grid->column('id','ID')->sortable();
        $grid->column('reserve_type_name','类型名称')->editable();
        $grid->column('reserve_type_image','类型封面')->lightbox(['width' => 50, 'height' => 50]);
        $grid->column('reserve_type_seting','类型菜品设置')->using(['1'=>'普通菜品','2'=>'菜品套餐']);
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        $grid->disableCreateButton();
        return $grid;
    }
    protected function form(){
        $form=new Form(new ReserveType());
        $form->text('reserve_type_name','类型名称');
        $form->image('reserve_type_image','类型封面')->rules('required|mimes:jpeg,bmp,png')->required();
        $form->radio('reserve_type_seting','类型菜品设置')->options(['1'=>'普通菜品','2'=>'菜品套餐'])->default(1);
        return $form;
    }
}