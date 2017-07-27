<?php

namespace Core\PubSub\Subscriptions;

use Core\Exceptions\MailerException;
use Core\Contracts\Models\User as UserModelContract;
use Core\Contracts\Infrastructure\Mailer as MailerContract;

class SendWelcomeEmailWithGeneratedPassword
{
    protected $mailer;

    public function __construct(MailerContract $mailer)
    {
        $this->mailer = $mailer;
    }

    public function call(UserModelContract $user, string $password)
    {
        $sent = $this->mailer->send(
            'sys@mail.core.com',
            'Core System',
            [$user->get('email') => $user->get('name')],
            'Welcome',
            "Welcome to Core. Here is your password: {$password}." // TODO: Change me
        );

        if (!$sent) {
            throw new MailerException(
                "Fail sending email to {$user->get('email')}.",
                500,
                $this->mailer->error()
            );
        }
    }
}
