<?php

namespace Test\Core\Autobots;

use Test\Core\Responses\TestResponse;
use Core\Transformer\Autobots\Autobot;

class AdvanceTestAutobot extends Autobot
{
    protected $responseClass = TestResponse::class;

    protected function collectResponseInstanceArgs($model): array
    {
        return [
            $this->transformBasicAttributes($model),
        ];
    }

    protected function basicAttribute(): array
    {
        return [
            ['id', self::TYPE_INT],
            ['name', self::TYPE_STRING],
            ['phone_number', self::TYPE_STRING],
        ];
    }
}
