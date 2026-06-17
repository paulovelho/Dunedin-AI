<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Magrathea2\MagratheaPHP;

MagratheaPHP::LoadVendor();
MagratheaPHP::Instance()
    ->AppPath(__DIR__ . '/..')
    ->AddCodeFolder(
        __DIR__ . '/..',
        __DIR__ . '/../features/User',
        __DIR__ . '/../features/Highlight',
        __DIR__ . '/../features/Note',
        __DIR__ . '/../features/File'
    )
    ->Load()
    ->Connect()
    ->StartSession();
