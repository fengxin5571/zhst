<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/25
 * Time: 7:29 PM
 */
namespace App\Admin\Controllers;
use App\Model\TakeFoodCategory;
use App\Model\TakeFoodPool;
use App\Model\TakeFoodTag;
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
        return $content->header('外卖菜品')
               ->description('列表')
               ->breadcrumb(['text'=>'外卖菜品'])
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
    public function edit($id,Content $content){
        return $content
            ->header('编辑菜品')
            ->description('编辑')
            ->breadcrumb(['text'=>'编辑菜品'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new TakeFoodPool());
        $grid->column('id','ID')->sortable();
        $grid->foodCategory()->cat_name('菜品分类');
        $grid->tags('菜品标签')->display(function($tags){
            $tags = array_map(function ($tag) {
                return "<span class='label label-info'>{$tag['t_tag_name']}</span>";
            }, $tags);
            return join('&nbsp;', $tags);
        });
        $grid->column('name','菜品名称')->editable();
        $grid->column('food_image','菜品封面')->lightbox(['width' => 50, 'height' => 50]);
        $grid->column('price','菜品价格')->display(function($price){
            return '￥'.$price;
        })->sortable();
        $grid->column('is_show','状态')->using(['0'=>'<span class=\'label label-danger\'>未上架</span>','1'=>'<span class=\'label label-success\'>已上架</span>']);
        $grid->column('sellCount','销量')->sortable();
        $grid->column('likeCount','点赞数')->sortable();
        $grid->column('is_recommend','特别推荐')->using(['0'=>'否','1'=>'是']);
        $grid->column('created_at','添加时间')->sortable();
        $grid->filter(function($filter){
            $filter->equal('cid','菜品分类')->select(TakeFoodCategory::pluck('cat_name','id'));
            $filter->where(function ($query) {
                $query->whereHas('tags',function ($query){
                    $query->where('t_tag_name','like',"%{$this->input}%");
                });
            }, '菜品标签','t_tag_name')->multipleSelect(TakeFoodTag::all()->pluck('t_tag_name','id'));
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
        $categroyList=TakeFoodCategory::pluck('cat_name','id')->toArray();
        $form=new Form(new TakeFoodPool());
        $form->divider('基本信息');
        $form->select('cid','菜品分类')->options($categroyList)->required();
        $form->text('name','菜品名称')->rules('required');
        $form->textarea('description','菜品简介')->rows(5);
        $form->image('food_image','菜品封面')->rules('required|mimes:jpeg,bmp,png')->required();
        $form->currency('price','菜品价格')->symbol('￥')->required();
        $form->radio('is_show','状态')->options(['0'=>'未上架','1'=>'已上架'])->default(1)->required();
        $form->radio('is_recommend','特别推荐')->options(['0'=>'否','1'=>'是'])->default(0);
        $form->divider('规格参数');
        $form->number('weight','菜品重量(单位：K)')->min(1)->default(1);
        $form->number('calorie','卡路里(100K)')->min(0)->default(0);
        $form->multipleSelect('tags','菜品标签')->options(TakeFoodTag::all()->pluck('t_tag_name','id'));
        return $form;
    }
}