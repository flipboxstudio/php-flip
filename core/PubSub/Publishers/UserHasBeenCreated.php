<?php

namespace Core\PubSub\Publishers;

use Core\Contracts\PubSub\Publisher as PublisherContract;
use Core\PubSub\Subscriptions\SendWelcomeEmailWithGeneratedPassword;

class UserHasBeenCreated implements PublisherContract
{
    public function subscribers(): array
    {
        return [
            SendWelcomeEmailWithGeneratedPassword::class => 1,
        ];
    }
}
