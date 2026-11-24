<?php

class Validator
{
    private array $errors = [];

    public function required(string $field, $value): self
    {
        if (trim((string) $value) === '') {
            $this->errors[$field] = ucfirst($field) . ' is required.';
        }
        return $this;
    }

    public function length(string $field, $value, int $min, int $max): self
    {
        $value = trim((string) $value);
        $length = strlen($value);

        if ($length < $min || $length > $max) {
            $this->errors[$field] = ucfirst($field) . ' must be between ' . $min . ' and ' . $max . ' characters.';
        }
        return $this;
    }

    public function intRange(string $field, $value, int $min, int $max): self
    {
        if (!filter_var($value, FILTER_VALIDATE_INT) || (int) $value < $min || (int) $value > $max) {
            $this->errors[$field] = ucfirst($field) . ' must be a number between ' . $min . ' and ' . $max . '.';
        }
        return $this;
    }

    public function in(string $field, $value, array $allowed): self
    {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field] = ucfirst($field) . ' is not valid.';
        }
        return $this;
    }

    public function unique(string $field, $value, callable $exists): self
    {
        if ($exists($value)) {
            $this->errors[$field] = ucfirst($field) . ' already exists.';
        }
        return $this;
    }

    public function matches(string $field, $value, string $pattern, string $message): self
    {
        if (!preg_match($pattern, (string) $value)) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function fail(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
