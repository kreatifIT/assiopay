<?php

namespace AssioPay;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Kreatif\Project\Project;
use rex_config;

class AssioPay
{

    /** @var Client $client */
    protected Client $client;

    /** @var string $accessToken */
    protected $accessToken;

    public function __construct()
    {
        $this->doLogin();
    }

    protected function doLogin(): void
    {
        $isSandbox = $this->getSettingValue('assiopay_use_sandbox');

        if ($isSandbox) {
            $baseUri = $this->getSettingValue('assiopay_sandbox_endpoint');
            $mail = $this->getSettingValue('assiopay_sandbox_mail');
            $password = $this->getSettingValue('assiopay_sandbox_password');
        } else {
            $baseUri = $this->getSettingValue('assiopay_live_endpoint');
            $mail = $this->getSettingValue('assiopay_live_mail');
            $password = $this->getSettingValue('assiopay_live_password');
        }
        try {
            $client = new Client([
                'base_uri' => $baseUri,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);
            $response = $client->post('user/login', [
                'body' => json_encode([
                    'mail' => $mail,
                    'password' => $password,
                ]),
                'Content-Type' => 'application/json',
            ]);
            $response = json_decode($response->getBody(), true);
            $this->accessToken = $response['accessToken'];

            // initialise new client with authorization header, so we don't need to define this for every request
            $this->client = new Client([
                'base_uri' => $baseUri,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
            ]);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $errorMessage = 'Authentication Error : ' . $responseBodyAsString;
            Project::sendFoodErrorMail($errorMessage);
        }
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
        try {
            $response = $this->client->post('admin/cardinfo', [
                'body' => json_encode([
                    'cardHash' => $cardHash,
                ]),
            ]);
            $data = json_decode($response->getBody(), true);
            $floatFields = ['balance', 'value', 'fractionedTicketValue', 'maxSpendableAmount'];
            foreach ($data as $key => $value) {
                if (in_array($key, $floatFields)) {
                    $data[$key] = (float)str_replace(',', '.', trim($value));
                }
            }
            return $data;
        } catch (ClientException $e) {
            // 404 = Card Hash not found
            if ($e->getCode() != 404) {
                $responseData = $e->getResponse();
                $responseBodyAsString = $responseData->getBody()->getContents();
                $errorMessage = 'Authentication Error : ' . $responseBodyAsString;
                Project::sendFoodErrorMail($errorMessage);
            }
            return null;
        }
    }


    public function getFoodTransactions(string $companyFiscalCode, string $workerFiscalCode, \DateTime $startDate, \DateTime $endDate): ?array
    {
        try {
            $response = $this->client->post('admin/transactions', [
                'query' => [
                    'dataDA' => $startDate->format('d/m/Y'),
                    'dataA' => $endDate->format('d/m/Y'),
                    'partitaIvaAzienda' => $companyFiscalCode,
                    'codFiscaleUtente' => $workerFiscalCode,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            if (isset($data['transactionResponseList'])) {
                foreach ($data['transactionResponseList'] as &$value) {
                    $value['amount'] = $value['amount'] / 100;
                }
                return $data['transactionResponseList'];
            }
        } catch (BadResponseException $e) {
            $responseData = $e->getResponse();
            $responseBodyAsString = $responseData->getBody()->getContents();
            $errorMessage = 'Authentication Error : ' . $responseBodyAsString;
            Project::sendFoodErrorMail($errorMessage);
        }
        return null;
    }
}


