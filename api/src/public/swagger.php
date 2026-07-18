<?php

include("../inc.php");
use Magrathea2\MagratheaPHP;

// $swaggerFile = MagratheaPHP::Instance()->GetAppRoot()."/../../openapi.yaml";
// $swaggerFile = realpath($swaggerFile);
$swaggerFile = realpath(__DIR__."/openapi.yaml");
if (!$swaggerFile || !file_exists($swaggerFile)) {
	http_response_code(404);
	die($swaggerFile." not found");
}
header("Content-Type: application/yaml");
header("Access-Control-Allow-Origin: *");
readfile($swaggerFile);
