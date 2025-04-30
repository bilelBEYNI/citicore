<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsService
{
    private $client;
    private $apiKey;
    private $senderNumber;

    public function __construct(HttpClientInterface $client, string $apiKey, string $senderNumber)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->senderNumber = $senderNumber;
    }

    public function sendSms(string $to, string $message): void
    {
        $this->client->request('POST', 'https://d9xeev.api.infobip.com/sms/2/text/advanced', [
            'headers' => [
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'messages' => [[
                    'from' => $this->senderNumber,
                    'destinations' => [['to' => $to]],
                    'text' => $message,
                ]],
            ],
        ]);
    }
}
