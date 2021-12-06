<?php

namespace Bluex\SocketIo\Providers;

use Bluex\SocketIo\InstallCommand;
use Bluex\SocketIo\Services\SocketIo;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SocketIoServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(BroadcastManager $broadcastManager)
    {
        $broadcastManager->extend('socket-io', function (Application $app, array $config) {
            return new SocketIo();
        });
    }


    /**
     * Register the package services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
