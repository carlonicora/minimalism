<?php
header("Access-Control-Allow-Origin: *");
if (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, referrer, utm-campaign, utm-medium, utm-source, utm-term");
    header("Access-Control-Allow-Methods: OPTIONS, GET, POST, DELETE, PUT, PATCH");
    header("Allow: OPTIONS, GET, POST, DELETE, PUT, PATCH");
    http_response_code(200);
    echo(0);
    exit;
}

/** @noinspection PhpIncludeInspection */
require_once '../../../../vendor/autoload.php';

use CarloNicora\Minimalism\Minimalism;
$minimalism = new Minimalism();
$minimalism->render();