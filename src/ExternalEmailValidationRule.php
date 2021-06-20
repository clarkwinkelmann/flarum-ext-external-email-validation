<?php

namespace ClarkWinkelmann\ExternalEmailValidation;

use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class ExternalEmailValidationRule implements Rule
{
    protected $client;
    protected $settings;
    protected $errorMessage;

    public function __construct(Client $client, SettingsRepositoryInterface $settings)
    {
        $this->client = $client;
        $this->settings = $settings;
    }

    public function passes($attribute, $value): bool
    {
        $uri = $this->substitutePlaceholders($this->settings->get('external-email-validation.uri'), $value);

        $headers = [];

        foreach (explode("\n", $this->settings->get('external-email-validation.headers')) as $line) {
            $lineTrim = trim($line);

            if (empty($lineTrim)) {
                continue;
            }

            $parts = explode(':', $lineTrim, 2);

            if (count($parts) < 2) {
                throw new \Exception('Invalid request header line for email validation: "' . $lineTrim . '"');
            }

            $headers[trim($parts[0])] = trim($parts[1]);
        }

        $response = $this->client->request(
            $this->settings->get('external-email-validation.method') ?: 'POST',
            $uri,
            [
                'body' => $this->substitutePlaceholders($this->settings->get('external-email-validation.body'), $value),
                'http_errors' => false,
                'headers' => $headers,
            ]
        );

        $status = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();

        if ($errorKey = $this->settings->get('external-email-validation.response-error-key')) {
            // Decode JSON but do not throw errors
            $responseData = json_decode($responseBody, true);

            $this->errorMessage = $responseData ? Arr::get($responseData, $errorKey) : null;
        }

        if ($status >= 200 && $status < 300) {
            if ($this->settings->get('external-email-validation.use-response-status')) {
                return true;
            }

            $responseData = \GuzzleHttp\json_decode($responseBody, true);

            return !!Arr::get($responseData, $this->settings->get('external-email-validation.response-success-key'));
        }

        if ($this->settings->get('external-email-validation.use-response-status') && $status >= 400 && $status < 500) {
            return false;
        }

        throw new \Exception('Error performing request to ' . $uri . ', got ' . $status . ' response code');
    }

    protected function substitutePlaceholders(string $text, string $email): string
    {
        // Placeholders are in the format {{ email | raw }}
        // We also catch {{ email }} to warn about invalid modifier
        return preg_replace_callback('~{{\s*([^|}]+?)(?:\s*\|\s*([^}]+?))?\s*}}~m', function ($matches) use ($email): string {
            if ($matches[1] !== 'email') {
                throw new \Exception('Invalid placeholder attribute "' . $matches[1] . '". Available: email');
            }

            switch ($matches[2]) {
                case 'raw':
                    return $email;
                case 'json':
                    return \GuzzleHttp\json_encode($email);
                case 'urlencode':
                    return urlencode($email);
                default:
                    throw new \Exception('Invalid placeholder cast "' . $matches[2] . '". Available: raw, json, urlencode');
            }
        }, $text);
    }

    public function message(): string
    {
        if ($this->errorMessage) {
            return $this->errorMessage;
        }

        if ($defaultMessage = $this->settings->get('external-email-validation.default-error-message')) {
            return $defaultMessage;
        }

        /**
         * @var Translator $translator
         */
        $translator = resolve(Translator::class);

        return $translator->trans('clarkwinkelmann-external-email-validation.api.email-validation-error');
    }
}
