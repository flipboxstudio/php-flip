<?php

namespace Test\Core\PubSub\Publishers;

use Test\Core\PubSub\Subscriptions;
use Core\Contracts\PubSub\Publisher as PublisherContract;

class SequentialPublisher implements PublisherContract
{
    public function subscribers(): array
    {
        return [
            Subscriptions\FirstSubscription::class => 1,
            Subscriptions\SecondSubscription::class => 2,
            Subscriptions\ThirdSubscription::class => 3,
        ];
    }
}
