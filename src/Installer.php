<?php

namespace Bluex\SocketIo;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer
{
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        copy('../server.js', 'socket-server.js');
    }

    public static function postAutoloadDump(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        require $vendorDir . '/autoload.php';
        copy('../server.js', 'socket-server.js');

    }

    public static function postPackageInstall(PackageEvent $event)
    {
        $installedPackage = $event->getOperation()->getPackage();
        // do stuff
        copy('../server.js', 'socket-server.js');
    }

    public static function warmCache(Event $event)
    {
        // make cache toasty
    }
}
