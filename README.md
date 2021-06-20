# External Email Validation

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/clarkwinkelmann/flarum-ext-external-email-validation.svg)](https://packagist.org/packages/clarkwinkelmann/flarum-ext-external-email-validation) [![Total Downloads](https://img.shields.io/packagist/dt/clarkwinkelmann/flarum-ext-external-email-validation.svg)](https://packagist.org/packages/clarkwinkelmann/flarum-ext-external-email-validation) [![Donate](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.me/clarkwinkelmann)

This extension adds a rule to Flarum email validation that will use an external API request to validate the email.

This rule will apply everywhere an email is checked against the `UserValidator` including registration and email change requests.

The format of the external API request can be customized to meet a range of needs.

The data is expected to be passed in one of two ways: via query string, or via HTTP body.

The response can be read in one of two ways: a JSON key in the response, or the HTTP status code.

Additionally, an error message can optionally be read from a JSON key in the response.
Otherwise, a default error message can be provided in the extension settings.

To inject the email value inside the **URI** or **Body** settings, use one of the following strings in the template:

- `{{ email | urlencode }}`: This value will be replaced with the email value passed through PHP's `urlencode()` method
- `{{ email | json }}`: This value will be replaced with the email value passed through PHP's `json_encode()`. This means the JSON string delimiters will be included!
- `{{ email | raw }}`: This value will be replaced with the email value without any modification. Don't use this in the URI or JSON payload if you don't know what you're doing.

## Installation

    composer require clarkwinkelmann/flarum-ext-external-email-validation

## Support

This extension is under **minimal maintenance**.

It was developed for a client and released as open-source for the benefit of the community.
I might publish simple bugfixes or compatibility updates for free.

You can [contact me](https://clarkwinkelmann.com/flarum) to sponsor additional features or updates.

Support is offered on a "best effort" basis through the Flarum community thread.

## Integration tests

This extension has integration tests that rely on a local webserver running.

Run `composer test:server` to start the PHP development server on port 8000 with the docroot set to the `fixtures` folder.

Then you can run `composer test:setup` and `composer test` like regular Flarum integration tests.

## Links

- [GitHub](https://github.com/clarkwinkelmann/flarum-ext-external-email-validation)
- [Packagist](https://packagist.org/packages/clarkwinkelmann/flarum-ext-external-email-validation)
