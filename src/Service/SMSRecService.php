<?php

namespace App\Service;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class SMSRecService
{
    private Client $twilio;
    private string $from;

    public function __construct(string $sid, string $token, string $from)
    {
        $this->twilio = new Client($sid, $token);
        $this->from   = $from;
    }

    public function sendSms(string $to, string $message): bool
    {
        try {
            $this->twilio->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);
            return true;
        } catch (TwilioException $e) {
            // log($e->getMessage()) si besoin
            return false;
        }
    }


}