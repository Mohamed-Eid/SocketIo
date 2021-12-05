<?php


$file = 'server.js';
$newfile = 'ioserver.js';

if (!copy($file, $newfile)) {
    echo "failed to copy $file...\n";
}
