<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class MinLengthValidator extends AbstractValidator
{
    public function rule(): bool
    {
        $min = (int) ($this->args[0] ?? 0);
        return strlen((string) $this->value) >= $min;
    }

    public function validate(): string
    {
        $min = $this->args[0] ?? 0;
        return $this->message ?? "Поле '{$this->field}' должно содержать минимум {$min} символов";
    }
}