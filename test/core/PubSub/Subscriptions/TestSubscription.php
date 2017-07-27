<?php

namespace Test\Core\PubSub\Subscriptions;

use Core\Contracts\Container as ContainerContract;

class TestSubscription
{
    public function call(ContainerContract $container)
    {
        $container->instance('foo', 'bar');
    }

    public static function callStatic(ContainerContract $container)
    {
        $container->instance('foo', 'bar');
    }
}
