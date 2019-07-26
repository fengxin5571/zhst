<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->title('首页')
            ->description('仪表盘...')
           //->row(Dashboard::title())
            ->row(function (Row $row) {
                $row->column(3, function (Column $column) {
                    $column->append(Dashboard::todayOrder());
                });
                $row->column(3, function (Column $column) {
                    $column->append(Dashboard::todayOrder(2,'green'));
                });

                $row->column(3, function (Column $column) {
                   // $column->append(Dashboard::dependencies());
                });
            })
            ->row(Dashboard::environment());
    }
}
