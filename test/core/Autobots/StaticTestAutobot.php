<?php

namespace Test\Core\Autobots;

use Core\Util\Data\Fluent;
use Test\Core\Models\TestModel;
use Illuminate\Contracts\Support\Arrayable;

class StaticTestAutobot
{
    public static function transform(TestModel $model): Arrayable
    {
        return new Fluent([
            'id' => (int) $model->get('id'),
            'name' => (string) $model->get('name'),
        ]);
    }
}
