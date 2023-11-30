<?php

namespace AssioPay;

use GuzzleHttp\Client;
use rex_config;

class AssioPay
{

    /** @var Client|null */
    protected $client;

    public function __construct()
    {
        $this->doLogin();
    }

    protected function doLogin(): void
    {
        $isSandbox = $this->getSettingValue('assiopay_use_sandbox');

        if ($isSandbox) {
            $mail = $this->getSettingValue('assiopay_sandbox_mail');
            $password = $this->getSettingValue('assiopay_sandbox_password');
        } else {
            $mail = $this->getSettingValue('assiopay_live_mail');
            $password = $this->getSettingValue('assiopay_live_password');
        }
        $credentials = [
            'email' => $mail,
            'password' => $password,
        ];

        // TODO if request fails, send email to rex error address
        /*$this->client = new Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );*/
    }

    public function getSettingValue($key): string
    {
        return rex_config::get('assiopay', $key);
    }

    /**
     * @param string $cardHash
     * @return array
     */
    public function getCardInfoForCardHash(string $cardHash): ?array
    {
        return [
            'hash' => 'dasf345',
            'balance' => '500',
            'value' => '10',
            'fractionedTicketValue' => '30',
            'expiry' => '2024-06-06',
            'nofWholeTickets' => '3jk4df435',
        ];
    }
}

?>
