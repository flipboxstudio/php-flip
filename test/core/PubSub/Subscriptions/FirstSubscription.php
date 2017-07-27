<?php

namespace Test\Core\PubSub\Subscriptions;

use Core\Contracts\Container as ContainerContract;

class FirstSubscription
{
    public function call(ContainerContract $container)
    {
        $container->instance('number', [1 => 1]);
    }
}
