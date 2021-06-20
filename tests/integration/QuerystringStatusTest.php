<?php

namespace integration;

use Flarum\Testing\integration\TestCase;

class QuerystringStatusTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setting('external-email-validation.method', 'GET');
        $this->setting('external-email-validation.uri', 'http://0.0.0.0:8000/validate-querystring-status.php?email={{ email | urlencode }}');
        $this->setting('external-email-validation.body', '');
        $this->setting('external-email-validation.headers', '');
        $this->setting('external-email-validation.use-response-status', '1');
        $this->setting('external-email-validation.response-success-key', '');

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
}
