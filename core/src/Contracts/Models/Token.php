<?php

namespace Core\Contracts\Models;

interface Token extends Model
{
    public function getUser(): User;
}
