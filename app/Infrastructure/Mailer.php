<?php

namespace App\Infrastructure;

use PHPMailer;
use Core\Contracts\Infrastructure\Mailer as MailerContract;

class Mailer implements MailerContract
{
    protected $errors = [];

    public function __construct()
    {
        $this->mail = new PHPMailer();

        $this->mail->isSMTP();
        $this->mail->isHTML(true);
        $this->mail->Host = env('MAIL_HOST');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = env('MAIL_USERNAME');
        $this->mail->Password = env('MAIL_PASSWORD');
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = env('MAIL_PORT');
    }

    public function send(
        string $fromAddress,
        string $fromName,
        array $to,
        string $subject,
        string $content,
        array $cc = [],
        array $bcc = []
    ): bool {
        $this->mail->setFrom($fromAddress, $fromName);

        foreach ($to as $toAddress => $toName) {
            $this->mail->addAddress($toAddress, $toName);
        }

        foreach ($cc as $ccAddress => $ccName) {
            $this->mail->addCC($ccAddress, $ccName);
        }

        foreach ($bcc as $bccAddress => $bccName) {
            $this->mail->addBCC($bccAddress, $bccName);
        }

        $this->mail->Subject = $subject;
        $this->mail->Body = $content;

        try {
            $success = $this->mail->send();

            $this->errors = (array) $this->mail->ErrorInfo;
        } catch (Exception $e) {
            $this->errors = (array) $e->getMessage();

            $success = false;
        }

        return $success;
    }

    public function error(): array
    {
        return $this->errors;
    }
}
