<?php

namespace Test\Core\Autobots;

use Test\Core\Responses\TestResponse;
use Core\Transformer\Autobots\Autobot;
use Core\Contracts\Models\Model as ModelContract;

class AdvanceTestAutobot extends Autobot
{
    protected $responseClass = TestResponse::class;

    protected function responseParams(ModelContract $model): array
    {
        return [
            $this->__transform($model, $this->mapping()),
        ];
    }

    protected function basic(): array
    {
        return [
            ['id', self::TYPE_INT],
            ['name', self::TYPE_STRING],
            ['phone_number', self::TYPE_STRING],
        ];
    }
}
