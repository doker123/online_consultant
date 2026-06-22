<?php

namespace Validators;

use Src\Validator\AbstractValidator;
use Illuminate\Database\Capsule\Manager as DB;

class UniqueValidator extends AbstractValidator
{
    public function rule(): bool
    {
        $table = $this->args[0] ?? null;
        $column = $this->args[1] ?? $this->field;

        if (!$table) {
            return true;
        }

        $query = DB::table($table)->where($column, $this->value);

        if (!empty($this->args[2])) {
            $exceptColumn = $this->args[2];
            $exceptId = $this->args[3] ?? null;
            if ($exceptId) {
                $query->where($exceptColumn, '!=', $exceptId);
            }
        }

        return !$query->exists();
    }

    public function validate(): string
    {
        return $this->message ?? "Поле '{$this->field}' должно быть уникальным";
    }
}