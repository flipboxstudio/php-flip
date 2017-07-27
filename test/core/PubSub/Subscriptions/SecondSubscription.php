<?php

namespace Test\Core\PubSub\Subscriptions;

use Core\Contracts\Container as ContainerContract;

class SecondSubscription
{
    public function call(ContainerContract $container)
    {
        $number = $container->make('number');
        $number[2] = 2;
        $container->instance('number', $number);
    }
}
