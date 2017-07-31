<?php

namespace Core\Transformer\Autobots;

use Core\Responses\TokenResponse;
use Core\Contracts\Models\Model as ModelContract;
use Core\Contracts\Models\Token as TokenModelContract;

class TokenAutobot extends Autobot
{
    protected $responseClass = TokenResponse::class;

    protected $userAutobot;

    public function __construct(UserAutobot $userAutobot)
    {
        $this->userAutobot = $userAutobot;
    }

    protected function responseParams(ModelContract $model): array
    {
        return [
            $this->__transform($model, $this->mapping()),
            $model,
            $model->getUser(),
        ];
    }

    protected function basic(): array
    {
        return $this->common(
            ['id', 'created_at', 'updated_at'],
            [
                ['token', self::TYPE_STRING],
                ['expired_at', self::TYPE_DATETIME],
                ['user', function (TokenModelContract $model) {
                    return $this->userAutobot->transform(
                        $model->getUser()
                    );
                }],
            ]
        );
    }
}
