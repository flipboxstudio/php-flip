<?php

namespace Test\Core\Autobots;

use Core\Util\Data\Fluent;
use Test\Core\Responses\TestResponse;
use Core\Transformer\Autobots\Autobot;

class TestAutobot extends Autobot
{
    public function transform($model): Fluent
    {
        return new TestResponse(
            $this->get($model, 'id', self::TYPE_INT) +
            $this->get($model, 'name', self::TYPE_STRING)
        );
    }
}
