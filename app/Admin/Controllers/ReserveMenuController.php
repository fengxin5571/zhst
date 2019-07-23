<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/7/10
 * Time: 6:23 PM
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\ReserveMenuGender;
use App\Model\ReserveFoodPool;
use App\Model\ReserveMenu;
use App\Model\ReserveType;
use App\Model\PackageFood;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class ReserveMenuController extends AdminController{
    protected $weeklyList=[
        '1'=>'星期一',
        '2'=>'星期二',
        '3'=>'星期三',
        '4'=>'星期四',
        '5'=>'星期五',
    ];
    /**
     * 网订菜谱管理
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('菜谱管理')
            ->description('列表')
            ->breadcrumb(['text' => '菜谱管理'])
            ->body($this->grid());
    }

    /**
     * 新增菜谱
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增菜谱')
            ->description('新增')
            ->breadcrumb(['text' => '新增菜谱'])
            ->body($this->form());
    }
    protected function grid(){
        $reserveTypeList=ReserveType::all()->pluck('id')->toArray();
        $grid=new Grid(new ReserveMenu());
        //网订类型查询
        if (in_array(\Request::get('gender',1),$reserveTypeList)) {
            $grid->model()->where('reserve_type', \Request::get('gender',1));
        }
        $grid->column('id','ID')->sortable();
        $grid->column('菜品信息')->display(function(){

            if($this->food_type==1){//如果是普通菜品
               $food= ReserveFoodPool::where('id',$this->food_id)->first();
                return <<<EOT
             <div class="layui-table-cell laytable-cell-1-0-2">   
                 <p> 
                     <span style="padding-right: 10px">              
                          <img style="width: 30px;height: 30px;margin:0;cursor: pointer;" src="{$food['food_image']}" class="img img-thumbnail"> 
                     </span> 
                     <span>{$food['name']}</span> 
                     <span> | ￥{$food['price']}</span>
                 </p>   
             </div>   
EOT;
            }

        });
        $grid->column('weekly','星期排期')->help('每个数字代表对应的星期，如1；星期一')->label();
        $grid->column('created_at','添加时间')->sortable();
        $grid->tools(function ($tools) {
            $tools->append(new ReserveMenuGender());
        });
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
        });
        $grid->filter(function($filter){
            $filter->where(function ($query) {
                $query->whereRaw("find_in_set('".$this->input."',weekly)");
            },'星期排期')->radio($this->weeklyList);

        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new ReserveMenu());
        $form->select('reserve_type','网订类型')->options(ReserveType::all()->pluck('reserve_type_name','id'))->required();
        $form->multipleSelect('food_id','菜品')->options(ReserveFoodPool::where('is_show',1)->pluck('name','id'))->required()->help('可多选菜品');
        $form->checkbox('weekly','星期排期')->options($this->weeklyList);
        $form->saving(function (Form $form){
            $message=['title'=> '错误'];
            try{
                $weekly=implode(',',array_filter($form->weekly));
                if(!$weekly){
                    $message['message']='星期排期请至少选一项';
                    $error = new MessageBag($message);
                    return back()->with(compact('error'));
                }
                $food_id=array_filter($form->food_id);
                foreach ($food_id as $k=>$v){
                    //查询当前菜谱的星期排期
                    $weeklyArray=ReserveMenu::where(['reserve_type'=>$form->reserve_type,'food_id'=>$v])->value('weekly')?:[];
                    //比对数组的不同
                    $diffArray=array_intersect(array_filter($form->weekly),$weeklyArray);
                    if($diffArray){//有重复
                        $text='';
                        foreach ($diffArray as $v){
                            $text.=$this->weeklyList[$v].'&nbsp;&nbsp;&nbsp;';
                        }
                        $message['message']="选择的菜品已出现于{$text} 请勿重复添加!";
                        $error = new MessageBag($message);
                        return back()->with(compact('error'));
                    }else{//无重复
                        $reservMenu=ReserveMenu::where(['reserve_type'=>$form->reserve_type,'food_id'=>$v])->first();
                        if($reservMenu){//有值
                            $update=array_merge($reservMenu->weekly,array_filter($form->weekly));
                            asort($update);
                            $reservMenu->update(['weekly'=>implode(',',$update)]);
                        }else{//无值
                            $insert=[
                                'reserve_type'=>$form->reserve_type,
                                'food_id'=>$v,
                                'food_type'=>1,
                                'weekly'   =>$weekly,
                                'created_at'=>date('Y-m-d h:i:s', time()),
                                'updated_at'=>date('Y-m-d h:i:s', time()),
                            ];
                            ReserveMenu::create($insert);
                        }
                    }

                }
                return redirect('/admin/food/reserveMenu');
            }catch (\Exception $e){
                $message['message']="添加失败!".$e->getMessage().' line:'.$e->getLine();
                $error = new MessageBag($message);
                return back()->with(compact('error'));
            }

        });
        return $form;
    }
}