<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/26
 * Time: 9:19 AM
 */
namespace App\Admin\Controllers;
use App\Model\Discover;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class DiscoverController extends AdminController{
    /**
     * 智慧发现列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('智慧发现')
               ->description('列表')
               ->breadcrumb(['text'=>'智慧发现'])
               ->body($this->grid());
    }

    /**
     * 新增智慧发现
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增发现')
            ->description('新增')
            ->breadcrumb(['text'=>'新增发现'])
            ->body($this->form());
    }

    /**
     * 编辑智慧发现
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id,Content $content){
        return $content->header('编辑发现')
            ->description('编辑')
            ->breadcrumb(['text'=>'编辑发现'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new Discover());
        $grid->column('id','ID')->sortable();
        $grid->column('title','标题')->editable();
        $grid->column('images','轮播图')->lightbox(['width' => 50, 'height' => 50]);
        $grid->column('created_at','创建时间')->sortable();
        return $grid;
    }
    protected function form(){
        $form=new Form(new Discover());
        $form->text('title','标题')->required();
        $form->multipleImage('images','轮播图')->rules('required|mimes:jpeg,bmp,png')->removable()->sortable();
        $form->textarea('description','发现简介')->rows();
        return $form;
    }
}