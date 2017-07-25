<?php

namespace Core\Responses;

use Core\Util\Data\Fluent;
use Core\Contracts\Models\User as UserModelContract;
use Core\Contracts\Models\Token as TokenModelContract;

class Token extends Fluent
{
    protected $token;

    protected $user;

    public function __construct(array $attributes, TokenModelContract $token, UserModelContract $user)
    {
        parent::__construct($attributes);

        $this->token = $token;
        $this->user = $user;
    }

    public function getToken(): TokenModelContract
    {
        return $this->token;
    }

    public function getUser(): UserModelContract
    {
        return $this->user;
    }
}
