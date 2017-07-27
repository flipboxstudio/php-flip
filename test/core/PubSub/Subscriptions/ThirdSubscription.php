<?php

namespace Test\Core\PubSub\Subscriptions;

use Core\Contracts\Container as ContainerContract;

class ThirdSubscription
{
    public function call(ContainerContract $container)
    {
        $number = $container->make('number');
        $number[3] = 3;
        $container->instance('number', $number);
    }
}
