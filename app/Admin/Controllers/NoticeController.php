<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/9/12
 * Time: 4:29 PM
 */
namespace App\Admin\Controllers;
use App\Model\Notice;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class NoticeController extends AdminController{
    /**
     * 公告列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('公告')
            ->description('列表')
            ->breadcrumb(['text'=>'公告'])
            ->body($this->grid());
    }

    /**
     * 新增公告
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('公告')
            ->description('新增')
            ->breadcrumb(['text'=>'新增公告'])
            ->body($this->form());
    }

    /**
     * 编辑公告
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id,Content $content){
        return $content->header('公告')
            ->description('编辑')
            ->breadcrumb(['text'=>'编辑公告'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new Notice());
        $grid->column('id','ID')->sortable();
        $grid->column('title','公告标题')->editable();
        $grid->column('created_at','创建时间')->sortable();
        return $grid;
    }
    protected function form(){
        $form=new Form(new Notice());
        $form->text('title','公告标题')->required();
        $form->textarea('content','公告内容')->rules('required');
        return $form;
    }
}