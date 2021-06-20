<?php

$email = $_GET['email'];

if (preg_match('~@company\.tld$~', $email) !== 1) {
    http_response_code(404);
}
