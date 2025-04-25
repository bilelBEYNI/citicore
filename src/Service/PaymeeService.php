<?php
namespace App\Service;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;


class PayPalClient
{
    private $client;

    public function __construct(string $clientId, string $clientSecret)
    {
        $environment = new SandboxEnvironment($clientId, $clientSecret);
        $this->client = new PayPalHttpClient($environment);
    }

    public function getClient(): PayPalHttpClient
    {
        return $this->client;
    }
}
