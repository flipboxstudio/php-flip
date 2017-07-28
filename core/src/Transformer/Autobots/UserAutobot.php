<?php

namespace Core\Transformer\Autobots;

use Core\Responses\UserResponse;

class UserAutobot extends Autobot
{
    protected $responseClass = UserResponse::class;

    protected function collectResponseInstanceArgs($model): array
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
