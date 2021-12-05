<?php

namespace Bluex\SocketIo\Providers;

use Bluex\SocketIo\Services\SocketIo;
use Bluex\SocketIo\Services\SocketIoService;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Console\Application;
use Illuminate\Support\ServiceProvider;

class SocketIoServiceProvider extends ServiceProvider
{
    public function boot(BroadcastManager $broadcastManager)
    {
        $broadcastManager->extend('socket-io', function (Application $app, array $config) {
            return new SocketIo();
        });
    }
}
