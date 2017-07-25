<?php

namespace Core\Contracts\Models;

interface Model
{
    public function primaryKey();

    public function get($field);

    public function set($field, $value);
}
