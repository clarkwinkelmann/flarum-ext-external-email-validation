<?php

namespace integration;

use Flarum\Testing\integration\TestCase;

class JsonAuthorizationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setting('external-email-validation.method', 'POST');
        $this->setting('external-email-validation.uri', 'http://0.0.0.0:8000/validate-json-authorization.php');
        $this->setting('external-email-validation.body', '{"email":{{ email | json }}}');
        $this->setting('external-email-validation.headers', 'Authorization: Token abcd');
        $this->setting('external-email-validation.use-response-status', '0');
        $this->setting('external-email-validation.response-success-key', 'valid');

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
    }

    /**
     * @test
     */
    public function failed_authorization()
    {
        $this->setting('external-email-validation.headers', 'Authorization: Token wrong');

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

        $this->assertEquals(500, $response->getStatusCode());
    }
}
