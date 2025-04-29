<?php
namespace App\Message;

use App\Message\ReminderEmailRec;
use App\Service\MailRecService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class HandlerEmailRec 
{
    public function __construct(private MailRecService $mailService) {}

    public function __invoke(ReminderEmailRec $message): void
    {
        $this->mailService->send(
            $message->getTo(),
            $message->getSubject(),
            $message->getText()
        );
    }
}