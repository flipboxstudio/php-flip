<?php

namespace Core\Transformer\Autobots;

use Core\Responses\UserResponse;
use Core\Contracts\Models\Model as ModelContract;

class UserAutobot extends Autobot
{
    protected $responseClass = UserResponse::class;

    protected function responseParams(ModelContract $model): array
    {
        return [
            $this->__transform($model, $this->mapping()),
            $model,
        ];
    }

    protected function basic(): array
    {
        return $this->common(
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
