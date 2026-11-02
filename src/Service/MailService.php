<?php

namespace App\Service;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class MailService
{
    public function __construct(
        private MailerInterface $mailer,
    ){}

    public function sendVerificationCode(string $to, string $code): void
    {
        // todo add website name from env
        $email = new Email()
            ->from('noreply@nexly.school')
            ->to($to)
            ->subject('Your verification code')
            ->text("Your verification code is: $code");

        $this->mailer->send($email);
    }

}
