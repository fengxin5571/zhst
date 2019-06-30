<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/30
 * Time: 8:41 AM
 */
namespace App\Admin\Controllers;
use App\Model\TakeFoodCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class  TakeFoodCategroyController extends AdminController{
    /**
     * 外卖菜品分类管理
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('外卖菜品分类')
            ->description('分类')
            ->breadcrumb(['text'=>'外卖菜品分类'])
            ->body($this->grid());
    }

    /**
     * 新增菜品分类
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增菜品分类')
            ->description('新增')
            ->breadcrumb(['text'=>'新增菜品分类'])
            ->body($this->form());
    }
    public function edit($id,Content $content){
        return $content->header('编辑菜品分类')
            ->description('编辑')
            ->breadcrumb(['text'=>'编辑菜品分类'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new TakeFoodCategory());
        $grid->column('id',"ID")->sortable();
        $grid->column('cat_name','分类名称')->editable();
        $grid->column('created_at','添加时间')->sortable();
        $grid->column('updated_at','更新时间')->sortable();
        $grid->filter(function($filter){
            // 在这里添加字段过滤器
            $filter->like('cat_name', '分类名称')->placeholder('请输入分类名称查询');
        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new TakeFoodCategory());
        $form->text('cat_name','分类名称')
            ->creationRules(['required','unique:take_food_category'])
            ->updateRules(['required', "unique:take_food_category,cat_name,{{id}}"]);
        return $form;
    }
}