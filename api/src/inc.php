<?php
require_once __DIR__ . '/vendor/autoload.php';

use Magrathea2\MagratheaPHP;

MagratheaPHP::LoadVendor();
$mag = MagratheaPHP::Instance();
$mag->appRoot = __DIR__;
$mag->magRoot = __DIR__;
$mag->AddCodeFolder(
        __DIR__,
        __DIR__ . '/features/User',
        __DIR__ . '/features/Highlight',
        __DIR__ . '/features/Note',
        __DIR__ . '/features/Import',
        __DIR__ . '/features/Import/ImportServices',
        __DIR__ . '/features/File'
    )
    ->Load()
    ->Connect()
    ->StartSession();
