<?php

namespace Test\Core\Autobots;

use Test\Core\Responses\TestResponse;
use Core\Transformer\Autobots\Autobot;
use Core\Contracts\Models\Model as ModelContract;

class SortedTestAutobot extends Autobot
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
            ['z', self::TYPE_INT],
            ['y', self::TYPE_INT],
            ['x', self::TYPE_INT],
        ];
    }
}
