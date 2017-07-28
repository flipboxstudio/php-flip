<?php

namespace Core\Transformer\Autobots;

use Core\Responses\TokenResponse;
use Core\Contracts\Models\Model as ModelContract;
use Core\Contracts\Models\Token as TokenModelContract;

class TokenAutobot extends Autobot
{
    protected $responseClass = TokenResponse::class;

    protected $user;

    public function __construct(UserAutobot $user)
    {
        $this->user = $user;
    }

    protected function collectResponseInstanceArgs(ModelContract $model): array
    {
        return [
            $this->transformBasicAttributes($model),
            $model,
            $model->getUser(),
        ];
    }

    protected function basicAttribute(): array
    {
        return $this->commonAttribute(
            ['id', 'created_at', 'updated_at'],
            [
                ['token', self::TYPE_STRING],
                ['expired_at', self::TYPE_DATETIME],
                ['user', function (TokenModelContract $model) {
                    return $this->user->transform(
                        $model->getUser()
                    );
                }],
            ]
        );
    }
}
