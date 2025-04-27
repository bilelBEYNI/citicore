<?php
namespace App\Service;

use App\Message\ReminderEmailRec;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class MailRecService
{
    private MailerInterface $mailer;
    private MessageBusInterface $bus;

    public function __construct(MailerInterface $mailer, MessageBusInterface $bus)
    {
        $this->mailer = $mailer;
        $this->bus    = $bus;
    }

    public function send(string $to, string $subject, string $text): void
    {
        $email = (new Email())
            ->from('medinichiheb2@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($text);

        $this->mailer->send($email);
    }

    public function scheduleReminder(string $to, string $subject, string $text, int $delaySeconds = 10): bool
    {
        try {
            $message = new ReminderEmailRec($to, $subject, $text);
            $this->bus->dispatch($message, [new DelayStamp($delaySeconds * 1000)]);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
