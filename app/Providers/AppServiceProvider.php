<?php

namespace App\Providers;

use App\Exceptions\ExampleException;
use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        if ($this->app->environment() == 'local') {
            $this->app->register(\Reliese\Coders\CodersServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //手机号验证
        Validator::extend('is_mobile',function ($attribute, $value, $parameters, $validator){
            return !!preg_match('/^(0|86|17951)?(13[0-9]|15[012356789]|166|17[3678]|18[0-9]|14[57])[0-9]{8}$/', $value);
        });
        $table = config('admin.extensions.config.table', 'admin_config');
        if (Schema::hasTable($table)) {
            Config::load();
        }
    }
}
