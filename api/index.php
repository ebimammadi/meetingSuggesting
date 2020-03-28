<?php
include_once "api.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$request = isset($_REQUEST["request"]) ? trim($_REQUEST["request"]) : "";
validate_request_methods($request);

generate_request_response($request);

exit();
