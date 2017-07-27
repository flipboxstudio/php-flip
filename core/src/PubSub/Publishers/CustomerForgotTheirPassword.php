<?php

namespace Core\PubSub\Publishers;

use Core\Contracts\PubSub\Publisher as PublisherContract;
use Core\PubSub\Subscriptions\SendForgotPasswordLinkViaEmail;

class CustomerForgotTheirPassword implements PublisherContract
{
    public function subscribers(): array
    {
        return [
            SendForgotPasswordLinkViaEmail::class => 1,
        ];
    }
}
