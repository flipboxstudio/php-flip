<?php

namespace Core\Contracts\Infrastructure;

interface Mailer
{
    public function send(
        string $fromAddress,
        string $fromName,
        array $to,
        string $subject,
        string $content,
        array $cc = [],
        array $bcc = [],
        array $attachments = []
    ): bool;
}
