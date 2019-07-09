<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/9
 * Time: 9:43 AM
 */
namespace App\Admin\Controllers;
use App\Model\ReserveFoodCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ReserveFoodCategoryController extends AdminController{
    /**
     * 网订分类管理
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('网订分类')
            ->description('列表')
            ->breadcrumb(['text' => '网订分类'])
            ->body($this->grid());
    }
    /**
     * 新增网订分类
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增网订分类')
            ->description('详细')
            ->breadcrumb(['text' => '新增网订分类'])
            ->body($this->form());
    }
    public function edit($id,Content $content){
        return $content->header('编辑网订分类')
            ->description('详细')
            ->breadcrumb(['text' => '编辑网订分类'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new ReserveFoodCategory());
        $grid->column('id',"ID")->sortable();
        $grid->column('cat_name','分类名称')->editable();
        $grid->column('created_at','添加时间')->sortable();
        $grid->column('updated_at','更新时间')->sortable();
        $grid->filter(function ($filter){
            $filter->like('cat_name','分类名称')->placeholder('请输入分类名称查询');
        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new ReserveFoodCategory());
        $form->text('cat_name','分类名称')->rules('required');
        return $form;
    }
}