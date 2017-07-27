<?php

namespace Core\PubSub\Publishers;

use Core\PubSub\Subscriptions\SendWelcomeEmail;
use Core\Contracts\PubSub\Publisher as PublisherContract;

class CustomerHasBeenRegistered implements PublisherContract
{
    public function subscribers(): array
    {
        return [
            SendWelcomeEmail::class => 1,
        ];
    }
}
