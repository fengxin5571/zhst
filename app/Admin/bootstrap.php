<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);
app('view')->prependNamespace('admin', resource_path('views/admin'));
//表单初始化
\Encore\Admin\Form::init(function(\Encore\Admin\Form $form){
    $form->disableEditingCheck();
    $form->disableCreatingCheck();
    $form->disableViewCheck();
    $form->tools(function (\Encore\Admin\Form\Tools $tools){
        $tools->disableDelete();
        $tools->disableView();
        $tools->disableList();
    });
});
//表格初始化
Encore\Admin\Grid::init(function (Encore\Admin\Grid $grid){
    $grid->enableHotKeys();
    $grid->filter(function($filter){
        // 去掉默认的id过滤器
        $filter->disableIdFilter();
    });
    $grid->actions(function ($actions) {
        $actions->disableView();
    });
    $grid->disableExport();
    $grid->disableColumnSelector();
    $grid->paginate(20);
});
Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    //$navbar->right(new \App\Admin\Extensions\Nav\Links());
});