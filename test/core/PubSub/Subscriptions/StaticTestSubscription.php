<?php

namespace Test\Core\PubSub\Subscriptions;

use Core\Contracts\Container as ContainerContract;

class StaticTestSubscription
{
    public static function callStatic(ContainerContract $container)
    {
        $container->instance('foo', 'bar');
    }
}
