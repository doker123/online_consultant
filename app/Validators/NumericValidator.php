<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class NumericValidator extends AbstractValidator
{
    public function rule(): bool
    {
        return is_numeric($this->value);
    }

    public function validate(): string
    {
        return $this->message ?? "Поле '{$this->field}' должно быть числом";
    }
}