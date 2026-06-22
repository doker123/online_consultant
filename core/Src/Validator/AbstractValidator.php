<?php

namespace Src\Validator;

abstract class AbstractValidator
{
    protected string $field;
    protected mixed $value;
    protected array $args;
    protected ?string $message;

    public function __construct(string $field, mixed $value, array $args = [], ?string $message = null)
    {
        $this->field = $field;
        $this->value = $value;
        $this->args = $args;
        $this->message = $message;
    }

    abstract public function rule(): bool;

    abstract public function validate(): string;

    protected function getDefaultMessage(): string
    {
        return "Ошибка валидации поля {$this->field}";
    }
}
