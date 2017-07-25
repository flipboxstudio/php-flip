<?php

namespace App\Repositories;

use App\Token as TokenModel;
use Core\Contracts\Models\User as CoreUserModelContract;
use Core\Contracts\Models\Token as CoreTokenModelContract;
use Core\Contracts\Repositories\Token as CoreTokenRepositoryContract;

class TokenRepository extends EloquentRepository implements CoreTokenRepositoryContract
{
    public function makeFromUser(CoreUserModelContract $user): CoreTokenModelContract
    {
        return $user->tokens()->make();
    }

    protected function modelClassName(): string
    {
        return TokenModel::class;
    }
}
