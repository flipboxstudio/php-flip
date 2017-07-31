<?php

namespace Test\Core\PubSub\Subscriptions;

use Core\Contracts\Container as ContainerContract;

class InvokableTestSubscription
{
    public function __invoke(ContainerContract $container)
    {
        $container->instance('foo', 'bar');
    }
}
