<?php

namespace Core\Validator\Rules;

abstract class Rule
{
    protected $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    protected function getAttribute(string $attribute, $default)
    {
        return (isset($this->attributes[$attribute]))
            ? $this->attributes[$attribute]
            : ($default ?: null);
    }

    abstract public function rules(): array;
}
