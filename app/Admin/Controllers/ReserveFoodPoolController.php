<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/10
 * Time: 8:18 AM
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\ReserveFoodToday;
use App\Admin\Extensions\ResrveFoodRelpy;
use App\Admin\Extensions\Tools\SubSupplyimport;
use App\Imports\ReserveFoodImport;
use App\Model\ReserveFoodCategory;
use App\Model\ReserveFoodPool;
use App\Model\ReserveFoodReply;
use App\Model\ReserveFoodTag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;
class ReserveFoodPoolController extends AdminController{
    protected $today=['1'=>'早餐','2'=>'午餐','3'=>'加班餐'];
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
        $grid->column('name','菜品名称')->editable();
        $grid->column('food_image','菜品封面')->lightbox(['width' => 50, 'height' => 50]);
        $grid->column('cook','厨师')->display(function($cook){
            $text='暂无厨师';
            if($cook){
                $text=$cook;
            }
            return $text;
        });
        $grid->column('price','菜品价格')->display(function($price){
            return '￥'.$price;
        })->sortable();
        $grid->column('likeCount','点赞数')->sortable();
        $grid->column('评论数')->display(function(){
            return ReserveFoodReply::where('food_id',$this->id)->count();
        })->expand(function ($model) {
            $comments = $model->comments()->take(10)->orderBy('created_at','desc')->get()->map(function ($comment) {
                return $comment->only(['id','reply_name','comment', 'created_at']);
            });
            return new Table(['ID', '评论人','评论内容', '发布时间'], $comments->toArray());
        });
        $grid->column('is_new','新品推荐')->using([0=>'否','1'=>'是']);
        $grid->column('is_exchange','厨艺交流')->using(['0'=>'否','1'=>'是']);
        $grid->column('is_health','健康养生')->using(['0'=>'否','1'=>'是']);
        $grid->column('is_show','状态')->using(['0'=>'<span class=\'label label-danger\'>未上架</span>','1'=>'<span class=\'label label-success\'>已上架</span>']);
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
        $grid->tools(function ($tools) {
//            $tools->batch(function ($batch) {
//                $batch->add('今日菜谱-无', new ReserveFoodToday(2));
//                $batch->add('今日菜谱-早餐', new ReserveFoodToday(0));
//                $batch->add('今日菜谱-午餐', new ReserveFoodToday(1));
//            });
            $tools->append(new SubSupplyimport(2));
        });
        $grid->actions(function ($actions) {

            $actions->append(new ResrveFoodRelpy($actions->getKey()));
            $actions->disableView();
        });
        return $grid;
    }
    protected function form(){
        $categroyList=ReserveFoodCategory::pluck('cat_name','id')->toArray();
        $form=new Form(new ReserveFoodPool);
        $form->divider('首页推荐');
        $form->checkbox('is_recommend','所属类型')->options(['1'=>'早餐','2'=>'午餐'])->default(0);
        $form->divider('今日菜谱-领导');
        $form->checkbox('is_today_leader','所属类型')->options($this->today)->default(0);
        $form->divider('今日菜谱-员工');
        $form->checkbox('is_today_employ','所属类型')->options($this->today)->default(0);
        $form->divider('基本信息');
        $form->select('cid','菜品分类')->options($categroyList)->required();
        $form->text('name','菜品名称')->required();
        $form->textarea('description','菜品简介')->rows(5);
        $form->image('food_image','菜品封面')->rules('required|mimes:jpeg,bmp,png')->uniqueName()->required()->help('上传图片不得大于800K');
        $form->radio('is_show','状态')->options(['0'=>'未上架','1'=>'已上架'])->default(1)->required();
        $form->currency('price','菜品价格')->symbol('￥')->required();
        $form->radio('is_new','新品推荐')->options(['0'=>'否','1'=>'是'])->default(0);
        $form->divider('厨师信息');
        $form->text('cook','厨师')->required();
        $form->image('cook_image','厨师照片')->rules('required|mimes:jpeg,bmp,png')->uniqueName()->required()->help('上传图片不得大于800K');
        $form->text('cook_speciality','厨师特长')->required();
        $form->text('cook_group','厨师分组')->required();
        $form->divider('规格参数');
        $form->multipleSelect('tags','菜品标签')->options(ReserveFoodTag::all()->pluck('r_tag_name','id'));
        $form->divider('厨艺交流');
        $form->radio('is_exchange','厨艺交流')->options(['0'=>'否','1'=>'是'])->default(0);
        $form->UEditor('ex_content','做菜步骤')->options(['initialFrameHeight' => 500]);
        $form->divider('健康养生');
        $form->radio('is_health','健康养生')->options(['0'=>'否','1'=>'是'])->default(0);
        $form->multipleImage('health_images','菜品轮播图')->rules('mimes:jpeg,bmp,png')->uniqueName()->removable()->help('上传图片不得大于800K');
        $form->UEditor('health_content','养生菜谱')->options(['initialFrameHeight' => 500]);
        $form->saving(function(Form $form){
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
            if(request()->file('cook_image')){
                if(request()->file('cook_image')->getSize()>="819200"){
                    $message=[
                        'title'=>'错误',
                        'message'=>'厨师照片大小不能超过800K'
                    ];
                    $error=new MessageBag($message);
                    return back()->with(compact('error'));
                }
            }
            if(request()->file('health_images')){
                foreach (request()->file('health_images') as $file){
                    if($file->getSize()>="819200"){
                        $message=[
                            'title'=>'错误',
                            'message'=>'菜品轮播图片大小不能超过800K'
                        ];
                        $error=new MessageBag($message);
                        return back()->with(compact('error'));
                    }
                }
            }
            if(!$form->_editable){
//                if(empty((float)$form->price)){
//                    $message=[
//                        'title'=>'错误',
//                        'message'=>'菜品价格不能为空'
//                    ];
//                    $error=new MessageBag($message);
//                    return back()->with(compact('error'));
//                }
//                $form->is_today_leader=implode(',',array_filter($form->is_today_leader));
//                $form->is_today_employ=implode(',',array_filter($form->is_today_employ));
                //dd($form->is_today_leader);
            }

        });
        return $form;
    }
    public function today(Request $request){
        foreach (ReserveFoodPool::find($request->get('ids')) as $food) {
            $food->is_today = $request->get('type');
            $food->save();
        }
    }
    public function import(Content $content){
        $content->header('批量菜品导入');
        $content->description('导入数据');
        $content->breadcrumb(
            ['text' => '网订菜品', 'url' => '/food/reservePool'],
            ['text' => '导入数据']
        );
        $form=new \Encore\Admin\Widgets\Form();
        $form->action(route('reserve.import.post'));
        $form->file('importFile','Excel菜品文件：')->required()->rules('mimes:xlsx')
            ->help('请按给定的Excel格式文件上传，下载格式文件点击这里<a href="/storage/Excel/reservefood.xlsx" download="reservefood.xlsx" target="_blank">Excel格式文件</a>');
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
            Excel::import(new ReserveFoodImport(),$file);
            admin_toastr('导入成功', 'success',['timeOut'=>1000]);
            return redirect('/admin/food/reservePool');
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
