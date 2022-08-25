<?php

namespace App\Admin\Forms;

use Encore\Admin\Config\ConfigModel;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class Setting extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '外卖设置';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        //dump($request->all());
        ConfigModel::where('name','expire_time')->update(['value'=>$request->get('expire_time')]);
        if(strtotime($request->get('take_out_start'))>strtotime($request->get('take_out_end'))){
            admin_error('外卖下单开始时间大于结束时间');
            return back();
        }
        if(strtotime($request->get('get_time_start'))>strtotime($request->get('get_time_end'))){
            admin_error('外卖下单开始时间大于结束时间');
            return back();
        }
        ConfigModel::where('name','take_out_start')->update(['value'=>$request->get('take_out_start')]);
        ConfigModel::where('name','take_out_end')->update(['value'=>$request->get('take_out_end')]);
        ConfigModel::where('name','get_time_start')->update(['value'=>$request->get('get_time_start')]);
        ConfigModel::where('name','get_time_end')->update(['value'=>$request->get('get_time_end')]);
        ConfigModel::where('name','card_convert')->update(['value'=>$request->get('card_convert')]);
        admin_success('配置成功');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->datetime('expire_time','外卖超时(分钟)')->format('m');
        $this->timeRange('take_out_start','take_out_end','外卖下单时间');
        $this->timeRange('get_time_start','get_time_end','外卖取餐时间');
        $this->number('card_convert','一卡通点数金额比例')->min(1)->placeholder('请输入比例');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'expire_time'       => ConfigModel::where('name','expire_time')->value('value'),
            'take_out_start'       => ConfigModel::where('name','take_out_start')->value('value'),
            'take_out_end'       => ConfigModel::where('name','take_out_end')->value('value'),
            'get_time_start'       => ConfigModel::where('name','get_time_start')->value('value'),
            'get_time_end'       => ConfigModel::where('name','get_time_end')->value('value'),
            'card_convert'   => ConfigModel::where('name','card_convert')->value('value')
        ];
    }
}
