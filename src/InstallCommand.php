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
