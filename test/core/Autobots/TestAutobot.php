<?php

namespace Test\Core\Autobots;

use Core\Util\Data\Fluent;
use Test\Core\Responses\TestResponse;
use Core\Transformer\Autobots\Autobot;

class TestAutobot extends Autobot
{
    public function transform(): Fluent
    {
        return new TestResponse(
            $this->get('id', self::TYPE_INT) +
            $this->get('name', self::TYPE_STRING)
        );
    }
}
