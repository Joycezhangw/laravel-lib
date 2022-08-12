<?php

namespace JoyceZ\LaravelLib;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use JoyceZ\LaravelLib\Contracts\Captcha as CaptchaContract;
use JoyceZ\LaravelLib\Services\Captcha\Image\Captcha;
use Laravel\Lumen\Application as LumenApplication;

class ServiceProvider extends LaravelServiceProvider
{

    public function boot()
    {
        $this->publishes([__DIR__ . '/config.php' => config_path('landao.php')]);
    }

    /**
     * 设置配置文件
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/config.php');
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('landao.php')], 'joyce-config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('landao');
        }

        $this->mergeConfigFrom($source, 'landao');
    }

    public function register()
    {
        $this->registerBindings();
        $this->commands('JoyceZ\LaravelLib\Generators\Commands\MakeRepositoryCommand');
        $this->commands('JoyceZ\LaravelLib\Generators\Commands\MakeEnumCommand');
    }

    protected function registerBindings()
    {
        /**
         * 绑定图形验证码
         */
        $this->app->bind(CaptchaContract::class, function () {
            $captcha = new Captcha();
            $config = collect(config('landao.captcha'))->filter(function ($value) {
                return !empty($value);
            })->toArray();
            $captcha->withConfig($config);
            return $captcha;
        });
    }

    public function provides()
    {
        return [];
    }

}
