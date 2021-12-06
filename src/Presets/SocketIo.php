<?php

namespace Bluex\SocketIo\Presets;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class SocketIo extends Preset
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        static::updatePackages(false);
        static::updateSocketServer();
        static::removeNodeModules();
    }

    /**
     * Update the given package array.
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages)
    {
        return [
            'socket.io'    => '^4.0.1',
            'body-parser'  => '^1.19.0',
            'express'      => '^4.17.1',
            'dotenv'       => '10.0.0',
        ] + Arr::except($packages, ['vue', 'vue-template-compiler']);
    }


    /**
     * Update the bootstrapping files.
     *
     * @return void
     */
    protected static function updateSocketServer()
    {
        copy(__DIR__ . '/stubs/server.js', base_path('server1.js'));
    }
}
