<?php

$data = json_decode(file_get_contents('php://input'), true);

$email = is_array($data) ? $data['email'] : '';

$response = ['valid' => preg_match('~@company\.tld$~', $email) === 1];

if (!$response['valid']) {
    $response['error'] = 'We did not recognise this email.';
}

echo json_encode($response);
