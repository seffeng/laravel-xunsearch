<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2020 seffeng
 */
namespace Seffeng\LaravelXunsearch;

use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Seffeng\LaravelXunsearch\Exceptions\XunsearchException;

class XunsearchServiceProvider extends BaseServiceProvider
{

    /**
     *
     * {@inheritDoc}
     * @see \Illuminate\Support\ServiceProvider::register()
     */
    public function register()
    {
        $this->registerAliases();
        $this->mergeConfigFrom($this->configPath(), 'xunsearch');

        $this->app->singleton('seffeng.laravel.xunsearch', function ($app) {
            $config = $app['config']->get('xunsearch');

            if ($config && is_array($config)) {
                return new Xunsearch($config);
            } else {
                throw new XunsearchException('Please execute the command `php artisan vendor:publish --tag="xunsearch"` first to generate xunsearch configuration file.');
            }
        });
    }

    /**
     *
     * @author zxf
     * @date    2020年5月20日
     */
    public function boot()
    {
        if ($this->app->runningInConsole() && $this->app instanceof LaravelApplication) {
            $this->publishes([$this->configPath() => config_path('xunsearch.php')], 'xunsearch');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('rsa');
        }
    }

    /**
     *
     * @author zxf
     * @date    2020年5月20日
     */
    protected function registerAliases()
    {
        $this->app->alias('seffeng.laravel.xunsearch', Xunsearch::class);
    }

    /**
     *
     * @author zxf
     * @date    2020年5月20日
     * @return string
     */
    protected function configPath()
    {
        return dirname(__DIR__) . '/config/xunsearch.php';
    }
}
