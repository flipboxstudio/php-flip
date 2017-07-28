<?php

namespace Test\Core\Models;

use Core\Contracts\Models\Model as ModelContract;

class TestModel implements ModelContract
{
    protected $attributes = [];

    public function get($field)
    {
        return array_key_exists($field, $this->attributes)
            ? $this->attributes[$field]
            : null;
    }

    public function set($field, $value)
    {
        $this->attributes[$field] = $value;
    }
}
