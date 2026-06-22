<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class DateValidator extends AbstractValidator
{
    public function rule(): bool
    {
        $format = $this->args[0] ?? 'Y-m-d';
        $date = \DateTime::createFromFormat($format, $this->value);
        return $date && $date->format($format) === $this->value;
    }

    public function validate(): string
    {
        $format = $this->args[0] ?? 'Y-m-d';
        return $this->message ?? "Поле '{$this->field}' должно содержать дату в формате {$format}";
    }
}