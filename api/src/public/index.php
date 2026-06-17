<?php
/** @var Magrathea2\MagratheaApi $api */
$api = require __DIR__ . '/api.php';

$api->Fallback(function () {
    header("Content-Type: text/html; charset=utf-8");
    readfile(__DIR__ . '/home.html');
});

$api->Run();
