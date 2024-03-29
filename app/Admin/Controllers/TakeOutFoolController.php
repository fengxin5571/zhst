<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/25
 * Time: 7:29 PM
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\Tools\SubSupplyimport;
use App\Http\Controllers\Api\TakeOutFoodController;
use App\Imports\TakeOutFoodImport;
use App\Model\TakeFoodCategory;
use App\Model\TakeFoodPool;
use App\Model\TakeFoodTag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;
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
        $grid->column('name','菜品名称');
        $grid->column('food_image','菜品封面')->lightbox(['width' => 50, 'height' => 50]);
        $grid->column('price','菜品价格')->display(function($price){
            return '￥'.$price;
        })->sortable();
        $grid->column('box_charge','餐盒费')->display(function($box_charge){
            return '￥'.$box_charge;
        })->sortable();
        $grid->column('stock','库存')->sortable();
        $grid->column('limit','限购');
        $grid->column('is_new','新品推荐')->using([0=>'否','1'=>'是']);
        $grid->column('is_show','状态')->using(['0'=>'<span class=\'label label-danger\'>未上架</span>','1'=>'<span class=\'label label-success\'>已上架</span>']);
//        $grid->column('sellCount','销量')->sortable();
//        $grid->column('likeCount','点赞数')->sortable();
//        $grid->column('is_recommend','特别推荐')->using(['0'=>'否','1'=>'是']);
        $grid->column('created_at','添加时间')->sortable();
        $grid->tools(function ($tools) {
            $tools->append(new SubSupplyimport(1));
        });
        $grid->filter(function($filter){
            $filter->equal('cid','菜品分类')->select(TakeFoodCategory::pluck('cat_name','id'));
            $filter->where(function ($query) {
                $query->whereHas('tags',function ($query){
                    $query->whereIn('t_tag_id',$this->input);
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
        $form->divider('首页推荐');
        $form->radio('is_recommend','首页推荐')->options(['0'=>'否','1'=>'是'])->default(0);
        $form->divider('基本信息');
        $form->select('cid','菜品分类')->options($categroyList)->required();
        $form->text('name','菜品名称')->rules('required');
        $form->textarea('description','菜品简介')->rows(5);
        $form->image('food_image','菜品封面')->rules('required|mimes:jpeg,bmp,png')->uniqueName()->required()->help('上传图片不得大于800K');
        $form->currency('ot_price','原价价格')->symbol('￥')->required();
        $form->currency('price','菜品价格')->symbol('￥')->required();
        $form->number('stock','库存')->required()->min('0')->default(0);
        $form->number('limit','限购')->required()->min(0)->default(0);
        $form->radio('is_new','新品推荐')->options(['0'=>'否','1'=>'是'])->default(0);
        $form->radio('is_show','状态')->options(['0'=>'未上架','1'=>'已上架'])->default(1)->required();
//
        $form->divider('规格参数');
        $form->currency('box_charge','餐盒费')->symbol('￥');
        $form->number('weight','菜品重量(单位：K)')->min(1)->default(1);
        $form->number('calorie','卡路里(100K)')->min(0)->default(0);
        $form->multipleSelect('tags','菜品标签')->options(TakeFoodTag::all()->pluck('t_tag_name','id'));
        $form->saving(function(Form $form){
            if(empty((float)$form->price)){
                $message=[
                    'title'=>'错误',
                    'message'=>'菜品价格不能为空'
                ];
                $error=new MessageBag($message);
                return back()->with(compact('error'));
            }
            if(request()->file('food_image')){
                if(request()->file('food_image')->getSize()>="819200"){
                    $message=[
                        'title'=>'错误',
                        'message'=>'菜品封面大小不能超过800K'
                    ];
                    $error=new MessageBag($message);
                    return back()->with(compact('error'));
                }
            }
        });
        return $form;
    }
    public function import(Content $content){
        $content->header('批量菜品导入');
        $content->description('导入数据');
        $content->breadcrumb(
            ['text' => '外卖菜品', 'url' => '/food/takeOutPool'],
            ['text' => '导入数据']
        );
        $form=new \Encore\Admin\Widgets\Form();
        $form->action(route('takeOut.import.post'));
        $form->file('importFile','Excel菜品文件：')->required()->rules('mimes:xlsx')
            ->help('请按给定的Excel格式文件上传，下载格式文件点击这里<a href="/storage/Excel/takefood.xlsx" download="takefood.xlsx" target="_blank">Excel格式文件</a>');
        $content->body('<div class="box box-info">'.$form->render().'</div>');
        return $content;
    }
    public function importPost(Request $request){
        try{
            $file=$request->file('importFile');
            if(!$file->isValid()){
                throw new \Exception('上传错误,请重新上传！');
            }
            if($file->getClientOriginalExtension()!='xlsx'){
                throw new \Exception('请上传Execl文件');
            }
            Excel::import(new TakeOutFoodImport(),$file);
            admin_toastr('导入成功', 'success',['timeOut'=>1000]);
            return redirect('/admin/food/takeOutPool');
        }catch (\Exception $e){
            $message=[
                'title'=>'错误',
                'message'=>$e->getMessage(),
            ];
            $error=new MessageBag($message);
            return back()->with(compact('error'));
        }
    }
}