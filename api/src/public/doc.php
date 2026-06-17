<?php
/** @var Magrathea2\MagratheaApi $api */
$api = require __DIR__ . '/api.php';

header("Content-Type: text/html; charset=utf-8");
echo "<!doctype html><html lang=\"en\"><head><meta charset=\"utf-8\"><title>Dunedin AI API &mdash; Endpoints</title></head><body>";
echo "<h1>Dunedin AI API &mdash; Endpoints</h1>";
echo "<p>Base address: <code>" . htmlspecialchars($api->GetAddress() ?? "") . "</code></p>";
$api->Debug();
echo "</body></html>";
