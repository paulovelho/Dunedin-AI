<?php
require_once __DIR__ . '/vendor/autoload.php';

use Magrathea2\MagratheaPHP;

MagratheaPHP::LoadVendor();
$mag = MagratheaPHP::Instance();
$mag->appRoot = __DIR__;
$mag->magRoot = __DIR__;
$mag->AddCodeFolder(
        __DIR__,
        __DIR__ . '/admin',
        __DIR__ . '/admin/Swagger',
        __DIR__ . '/features/Import/ImportServices'
    )
    ->AddFeature(
        "User",
        "Highlight",
        "Note",
        "Import",
        "File",
        "SharedLink"
    )
    ->Load()
    ->Connect()
    ->StartSession();
