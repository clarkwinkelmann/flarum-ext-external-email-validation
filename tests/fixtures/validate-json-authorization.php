<?php

if ($_SERVER['HTTP_AUTHORIZATION'] !== 'Token abcd') {
    http_response_code(401);
    die();
}

require 'validate-json-response.php';
