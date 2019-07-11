<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/10
 * Time: 1:36 PM
 */
namespace App\Admin\Controllers;
use App\Model\PackageFood;
use App\Model\ReserveFoodPool;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

class PackageFoodController extends AdminController{
    /**
     * 套餐管理
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('套餐管理')
            ->description('列表')
            ->breadcrumb(['text' => '套餐管理'])
            ->body($this->grid());
    }
    /**
     * 新增套餐
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增套餐')
            ->description('新增')
            ->breadcrumb(['text' => '新增套餐'])
            ->body($this->form());
    }

    /**
     * 编辑套餐
     * @param mixed $id
     * @param Content $content
     * @return Content|void
     */
    public function edit($id,Content $content){
        return $content->header('编辑套餐')
            ->description('编辑')
            ->breadcrumb(['text' => '编辑套餐'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new PackageFood());
        $grid->column('id','ID')->sortable();
        $grid->column('package_name','套餐名称')->editable();
        $grid->column('package_image','套餐封面')->lightbox(['width' => 50, 'height' => 50]);
        $grid->foods('套餐菜品')->display(function($foods){
            $foods = array_map(function ($food) {
                return <<<EOT
             <div class="layui-table-cell laytable-cell-1-0-2">   
                 <p> 
                     <span> 
                        <img style="width: 30px;height: 30px;margin:0;cursor: pointer;" src="{$food['food_image']}"> 
                     </span>  
                     <span>{$food['name']}</span>             
                     <span> | ￥{$food['price']}</span>
                 </p>   
             </div>   
EOT;
            }, $foods);

            return join('&nbsp;', $foods);
        });
        $grid->column('package_price','套餐价格')->display(function($package_price){
            return '￥'.$package_price;
        });
        $grid->column('is_show','状态')->using(['0'=>'<span class=\'label label-danger\'>未上架</span>','1'=>'<span class=\'label label-success\'>已上架</span>']);
        $grid->column('created_at','添加时间')->sortable();
        $grid->filter(function($filter){
            $filter->like('package_name','套餐名称')->placeholder('请输入套餐名称查询');
            $filter->equal('is_show','状态')->radio([
                ''   => '全部',
                0    => '未上架',
                1    => '已上架',
            ]);
        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new PackageFood());
        $form->text('package_name','套餐名称')->required();
        $form->image('package_image','套餐封面')->rules('required|mimes:jpeg,bmp,png')->required();
        $form->textarea('package_description','菜品简介')->rows(5);
        $form->currency('package_price','套餐价格')->symbol('￥')->required();
        $form->radio('is_show','状态')->options(['0'=>'未上架','1'=>'已上架'])->default(1)->required();
        $form->listbox('foods','套餐菜品')->options(ReserveFoodPool::where('is_show',1)->pluck('name','id'));
        $form->saving(function(Form $form){
            if(empty((float)$form->package_price)){
                $message=[
                    'title'=>'错误',
                    'message'=>'套餐价格不能为空',
                ];
                $error=new MessageBag($message);
                return back()->with(compact('error'));
            }
            if(!array_filter($form->foods)){
               $message=[
                   'title'=>'错误',
                   'message'=>'套餐菜品至少选一项',
               ];
               $error=new MessageBag($message);
               return back()->with(compact('error'));
            }

        });
        return $form;
    }
}