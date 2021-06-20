<?php

namespace ClarkWinkelmann\ExternalEmailValidation;

use Flarum\Extend;
use Flarum\User\UserValidator;
use Illuminate\Validation\Validator;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Validator(UserValidator::class))
        ->configure(function (UserValidator $flarumValidator, Validator $laravelValidator) {
            $rules = $laravelValidator->getRules();

            if (!array_key_exists('email', $rules)) {
                return;
            }

            $rules['email'][] = resolve(ExternalEmailValidationRule::class);

            $laravelValidator->setRules($rules);
        }),
];
