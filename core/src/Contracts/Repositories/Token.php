<?php

namespace Core\Contracts\Repositories;

use Core\Contracts\Models\User as UserModel;
use Core\Contracts\Models\Token as TokenModel;

interface Token extends Repository
{
    public function makeFromUser(UserModel $user): TokenModel;
}
