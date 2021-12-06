<?php

namespace Bluex\SocketIo;

use Bluex\SocketIo\Presets\SocketIo;
use Illuminate\Console\Command;
use InvalidArgumentException;

class InstallCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'io:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the socket io server';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $this->install();
        // dd('aaa');
        // $this->call('io:install');
        // if (static::hasMacro($this->argument('type'))) {
        //     return call_user_func(static::$macros[$this->argument('type')], $this);
        // }

        // if (!in_array($this->argument('type'), ['bootstrap', 'vue', 'react'])) {
        //     throw new InvalidArgumentException('Invalid preset.');
        // }

        // $this->{$this->argument('type')}();

        // if ($this->option('auth')) {
        //     $this->call('ui:auth');
        // }
    }

    /**
     * Install the "Socket" preset.
     *
     * @return void
     */
    protected function install()
    {
        SocketIo::install();

        $this->info('Socket Io Server installed successfully.');
        $this->comment('Please run "npm install" to install your socket server dependancies.');
    }
}
