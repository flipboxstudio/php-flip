<?php

namespace Core\Contracts\Models;

interface Model
{
    public function get($field);

    public function set($field, $value);
}
