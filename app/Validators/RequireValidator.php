<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class RequireValidator extends AbstractValidator
{
    public function rule(): bool
    {
        return !empty($this->value) && strlen(trim((string) $this->value)) > 0;
    }

    public function validate(): string
    {
        return $this->message ?? "Поле '{$this->field}' обязательно для заполнения";
    }
}