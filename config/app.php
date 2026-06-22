<?php
return [
    "auth" => \Src\Auth\Auth::class,
    "identity" => \Src\Auth\IdentityProvider::class,
    "validators" => [
        "required" => \Validators\RequireValidator::class,
        "unique" => \Validators\UniqueValidator::class,
        "date" => \Validators\DateValidator::class,
        "email" => \Validators\EmailValidator::class,
        "min" => \Validators\MinLengthValidator::class,
        "date_after" => \Validators\DateAfterValidator::class,
        "numeric" => \Validators\NumericValidator::class,
    ],
    "routeMiddleware" => [
        "auth" => \Middleware\AuthMiddleware::class,
        "role" => \Middleware\RoleMiddleware::class,
    ],
    "routeAppMiddleware" => [
        // "trim" => \Middleware\TrimMiddleware::class,
        // "specialChars" => \Middleware\SpecialCharsMiddleware::class,
        "csrf" => \Middleware\CSRFMiddleware::class,
    ],
];
