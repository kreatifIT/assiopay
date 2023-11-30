<?php

namespace AssioPay;

use GuzzleHttp\Client;

class AssioPay
{

    /** @var Client|null */
    protected $client;

    public function __construct()
    {
        $this->doLogin();
    }

    protected function doLogin(): bool
    {
        $this->client = new Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );
    }

    /**
     * @param string $cardHash
     * @return array
     */
    public function getCardInfoForCardHash(string $cardHash): array
    {
        return [];
    }
}

?>
