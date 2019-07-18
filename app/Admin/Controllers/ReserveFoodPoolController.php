<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/10
 * Time: 8:18 AM
 */
namespace App\Admin\Controllers;
use App\Model\ReserveFoodCategory;
use App\Model\ReserveFoodPool;
use App\Model\ReserveFoodTag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

class ReserveFoodPoolController extends AdminController{
    /**
     * 网订菜品列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('网订菜品')
            ->description('列表')
            ->breadcrumb(['text' => '网订菜品'])
            ->body($this->grid());
    }

    /**
     * 新增网订菜品
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增菜品')
            ->description('详细')
            ->breadcrumb(['text' => '新增菜品'])
            ->body($this->form());
    }
    /**
     * 编辑菜品
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id,Content $content){
        return $content->header('编辑菜品')
            ->description('编辑')
            ->breadcrumb(['text' => '编辑菜品'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new ReserveFoodPool);
        $grid->column('id','ID')->sortable();
        $grid->foodCategory()->cat_name('菜品分类');
        $grid->tags('菜品标签')->display(function($tags){
            $tags = array_map(function ($tag) {
                return "<span class='label label-info'>{$tag['r_tag_name']}</span>";
            }, $tags);
            return join('&nbsp;', $tags);
        });
        $grid->column('name','菜品名称');
        $grid->column('food_image','菜品封面')->lightbox(['width' => 50, 'height' => 50]);
        $grid->column('price','菜品价格')->display(function($price){
            return '￥'.$price;
        })->sortable();
        $grid->column('cook','厨师')->display(function($cook){
            $text='暂无厨师';
            if($cook){
                $text=$cook;
            }
            return $text;
        });
        $grid->column('is_show','状态')->using(['0'=>'<span class=\'label label-danger\'>未上架</span>','1'=>'<span class=\'label label-success\'>已上架</span>']);
        $grid->column('likeCount','点赞数')->sortable();
        $grid->column('created_at','添加时间')->sortable();
        $grid->filter(function($filter){
            $filter->equal('cid','菜品分类')->select(ReserveFoodCategory::pluck('cat_name','id'));
            $filter->where(function ($query) {
                $query->whereHas('tags',function ($query){
                    $query->whereIn('r_tag_id',$this->input);
                });
            }, '菜品标签','r_tag_name')->multipleSelect(ReserveFoodTag::all()->pluck('r_tag_name','id'));
            $filter->like('name','菜品名称');
            $filter->equal('is_show','状态')->radio([
                ''   => '全部',
                0    => '未上架',
                1    => '已上架',
            ]);
        });
        return $grid;
    }
    protected function form(){
        $categroyList=ReserveFoodCategory::pluck('cat_name','id')->toArray();
        $form=new Form(new ReserveFoodPool);
        $form->divider('基本信息');
        $form->select('cid','菜品分类')->options($categroyList)->required();
        $form->text('name','菜品名称')->required();
        $form->textarea('description','菜品简介')->rows(5);
        $form->image('food_image','菜品封面')->rules('required|mimes:jpeg,bmp,png')->required();
        $form->currency('ot_price','原价价格')->symbol('￥')->required();
        $form->currency('price','菜品价格')->symbol('￥')->required();
        $form->text('cook','厨师');
        $form->radio('is_show','状态')->options(['0'=>'未上架','1'=>'已上架'])->default(1)->required();
        $form->divider('规格参数');
        $form->number('weight','菜品重量(单位：K)')->min(1)->default(1);
        $form->number('calorie','卡路里(100K)')->min(0)->default(0);
        $form->multipleSelect('tags','菜品标签')->options(ReserveFoodTag::all()->pluck('r_tag_name','id'));
        $form->saving(function(Form $form){
            if(empty((float)$form->price)){
                $message=[
                    'title'=>'错误',
                    'message'=>'菜品价格不能为空'
                ];
                $error=new MessageBag($message);
                return back()->with(compact('error'));
            }
        });
        return $form;
    }
}
