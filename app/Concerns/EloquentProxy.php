<?php

namespace App\Concerns;

trait EloquentProxy
{
    public function get($field)
    {
        return $this->getAttribute($field);
    }

    public function set($field, $value)
    {
        return $this->setAttribute($field, $value);
    }
}
