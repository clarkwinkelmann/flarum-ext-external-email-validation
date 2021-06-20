<?php

namespace integration;

use Flarum\Testing\integration\TestCase;

class JsonResponseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setting('external-email-validation.method', 'POST');
        $this->setting('external-email-validation.uri', 'http://0.0.0.0:8000/validate-json-response.php');
        $this->setting('external-email-validation.body', '{"email":{{ email | json }}}');
        $this->setting('external-email-validation.headers', '');
        $this->setting('external-email-validation.use-response-status', '0');
        $this->setting('external-email-validation.response-success-key', 'valid');
        $this->setting('external-email-validation.default-error-message', 'Wrong email');
        $this->setting('external-email-validation.response-error-key', '');

        $this->extension('clarkwinkelmann-external-email-validation');
    }

    /**
     * @test
     */
    public function register_valid()
    {
        $response = $this->send($this->request('POST', '/api/users', [
            'authenticatedAs' => 1,
            'json' => [
                'data' => [
                    'attributes' => [
                        'username' => 'Testing',
                        "password" => "12345678",
                        'email' => 'testing@company.tld',
                    ],
                ],
            ],
        ]));

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function register_invalid()
    {
        $response = $this->send($this->request('POST', '/api/users', [
            'authenticatedAs' => 1,
            'json' => [
                'data' => [
                    'attributes' => [
                        'username' => 'Testing',
                        "password" => "12345678",
                        'email' => 'testing@example.com',
                    ],
                ],
            ],
        ]));

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertEquals('{"errors":[{"status":"422","code":"validation_error","detail":"Wrong email","source":{"pointer":"\/data\/attributes\/email"}}]}', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    public function register_invalid_with_message()
    {
        $this->setting('external-email-validation.response-error-key', 'error');

        $response = $this->send($this->request('POST', '/api/users', [
            'authenticatedAs' => 1,
            'json' => [
                'data' => [
                    'attributes' => [
                        'username' => 'Testing',
                        "password" => "12345678",
                        'email' => 'testing@example.com',
                    ],
                ],
            ],
        ]));

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertEquals('{"errors":[{"status":"422","code":"validation_error","detail":"We did not recognise this email.","source":{"pointer":"\/data\/attributes\/email"}}]}', $response->getBody()->getContents());
    }
}
