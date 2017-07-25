<?php

namespace Core\Transformer\Autobots;

use Core\Responses\User as UserReponse;
use Core\Contracts\Models\User as UserModelContract;

class User extends Autobot
{
    protected $transformableClass = UserModelContract::class;

    protected $responseClass = UserReponse::class;

    protected function gatherResponseConstructorParameters($model): array
    {
        return [
            $this->transformBasicAttributes($model),
            $model,
        ];
    }

    protected function basicAttribute(): array
    {
        return $this->commonAttribute(
            ['id', 'created_at', 'updated_at'],
            [
                ['name', self::TYPE_STRING],
                ['email', self::TYPE_STRING],
                ['phone', self::TYPE_STRING],
                ['address', self::TYPE_STRING],
                ['sex', self::TYPE_STRING],
                ['role', self::TYPE_STRING],
            ]
        );
    }
}
