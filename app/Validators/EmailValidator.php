<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class EmailValidator extends AbstractValidator
{
    public function rule(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validate(): string
    {
        return $this->message ?? "Поле '{$this->field}' должно содержать корректный email";
    }
}