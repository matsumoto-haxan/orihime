<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // サービスのバインディング（呼び出し名と実クラスの紐付け）
        $this->app->bind('ManagementService', 'App\Services\ManagementService');
        $this->app->bind('OrderService', 'App\Services\OrderService');


    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // DBのデフォルト文字列設定
        Schema::defaultStringLength(191);
    }
}
