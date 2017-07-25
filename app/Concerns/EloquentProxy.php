<?php

namespace App\Concerns;

trait EloquentProxy
{
    public function primaryKey()
    {
        return $this->getKey();
    }

    public function get($field)
    {
        return $this->getAttribute($field);
    }

    public function set($field, $value)
    {
        return $this->setAttribute($field, $value);
    }
}
