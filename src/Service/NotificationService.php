<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class NotificationService
{
    private $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function sendNewEventNotification(string $eventName, string $eventDate, ?string $lieu = null): void
    {
        $message = "Nouvel événement : {$eventName}";
        if ($lieu) {
            $message .= " à {$lieu}";
        }
        $message .= " le {$eventDate}";

        $this->flashBag->add('success', $message);
    }
}