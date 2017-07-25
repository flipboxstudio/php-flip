<?php

namespace App\Repositories;

use App\User as UserModel;
use Core\Contracts\Repositories\User as CoreUserRepositoryContract;

class UserRepository extends EloquentRepository implements CoreUserRepositoryContract
{
    protected function modelClassName(): string
    {
        return UserModel::class;
    }
}
