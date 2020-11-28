<?php

namespace Iscxy\Dingtalk;

use Illuminate\Support\ServiceProvider;

class DingtalkServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton(Dingtalk::class, function(){
            return new Dingtalk();
        });

        $this->app->alias(Dingtalk::class, 'dingtalk');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
         $this->publishes([
            __DIR__.'/config/dingtalk.php' => config_path('dingtalk.php'),
            __DIR__.'/migrations' => database_path('migrations'),
        ]);
    }

    public function provides()
    {
        return [Dingtalk::class, 'dingtalk'];
    }
}
