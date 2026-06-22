<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class DateAfterValidator extends AbstractValidator
{
    public function rule(): bool
    {
        $afterDate = $this->args[0] ?? null;
        if (!$afterDate) {
            return true;
        }

        $valueDate = strtotime($this->value);
        $compareDate = strtotime($afterDate);

        if ($valueDate === false || $compareDate === false) {
            return false;
        }

        return $valueDate > $compareDate;
    }

    public function validate(): string
    {
        $afterDate = $this->args[0] ?? '';
        return $this->message ?? "Поле '{$this->field}' должно содержать дату после {$afterDate}";
    }
}