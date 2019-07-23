<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/23
 * Time: 9:43 AM
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\ReserveFoodRelpyGender;
use App\Model\ReserveFoodPool;
use App\Model\ReserveFoodReply;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class ReserveFoodReplyController extends AdminController{



    /**
     * 网订评论管理
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        $food_id=\Request::get('food_id');
        if(!$food_id){
            admin_toastr('获取失败', 'error',['timeOut'=>1000]);
            return back();
        }
        return $content->header('评论管理')
            ->description('列表')
            ->breadcrumb(['text' => '评论管理'])
            ->body($this->grid($food_id));
    }
    protected function grid($food_id){
        $grid=new Grid(new ReserveFoodReply());
        $grid->model()->where('food_id',$food_id)->orderBy('created_at','desc');
        $grid->column('id','ID')->sortable();
        $grid->column('reply_name','评论人');
        $grid->column('comment','评论内容');
        $grid->column('created_at','添加时间')->sortable();
        $grid->disableCreateButton();
        $grid->actions(function($actions){
            $actions->disableEdit();
            $actions->disableView();
        });
        $grid->tools(function ($tools) {
            $tools->append(new ReserveFoodRelpyGender());
        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new ReserveFoodReply());
        return $form;
    }
}