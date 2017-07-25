<?php

namespace Core\Contracts\Models;

use IteratorAggregate;

interface User extends Model
{
    public function getTokens(): IteratorAggregate;
}
