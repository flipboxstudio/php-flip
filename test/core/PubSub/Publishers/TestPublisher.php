<?php

namespace Test\Core\PubSub\Publishers;

use Test\Core\PubSub\Subscriptions\TestSubscription;
use Core\Contracts\PubSub\Publisher as PublisherContract;

class TestPublisher implements PublisherContract
{
    public function subscribers(): array
    {
        return [
            TestSubscription::class => 1,
        ];
    }
}
