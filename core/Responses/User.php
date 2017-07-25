<?php

namespace Core\Responses;

use Core\Util\Data\Fluent;
use Core\Contracts\Models\User as UserModelContract;

class User extends Fluent
{
    protected $user;

    public function __construct(array $attributes, UserModelContract $user)
    {
        parent::__construct($attributes);

        $this->user = $user;
    }

    public function getUser(): UserModelContract
    {
        return $this->user;
    }
}
