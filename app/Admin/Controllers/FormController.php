<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/17
 * Time: 2:00 PM
 */
namespace App\Admin\Controllers;
use App\Admin\Forms\Routes;
use App\Admin\Forms\Setting;
use App\Admin\Forms\Sms;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Content;

class FormController extends AdminController{
    public function setting(Content $content){
        $forms=[
            'basic'=>Setting::class,
//            'sms'=>Sms::class,
//            'routes'=>Routes::class,
        ];
        return $content->header('系统设置')
               ->breadcrumb(['text'=>'系统设置'])
               ->body(Tab::forms($forms));
    }
}