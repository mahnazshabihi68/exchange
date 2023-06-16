<?php
/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Classes;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class SMSIR
{

    protected string $APIKey;

    protected string $SecretKey;

    protected string $base;

    /**
     * SMSIR constructor.
     */

    public function __construct()
    {

        $this->APIKey = config('settings.smsir_api_key');

        $this->SecretKey = config('settings.smsir_secret_key');

        $this->base = 'https://RestfulSms.com/api/';
    }

    /**
     * @return int
     * @throws GuzzleException
     */

    public function getCredit(): int
    {
        $request = $this->client(true)->get('https://RestfulSms.com/api/credit');

        if ($request->getStatusCode() != 201) {

            throw new Exception('Failed to get data from SMS.ir Api', $request->getStatusCode());

        }

        $request = json_decode($request->getBody()->getContents());

        if (!$request->IsSuccessful) {

            throw new Exception('Connection to SMS.ir Api was unsuccessful');

        }

        return $request->Credit;
    }

    /**
     * @param bool $isAuthenticated
     * @return Client
     * @throws GuzzleException
     */

    private function client(bool $isAuthenticated = false): Client
    {
        $headers = [
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ];

        if ($isAuthenticated) {

            $headers['x-sms-ir-secure-token'] = $this->generateToken();

        }

        return new Client([
            'headers' => $headers,
            'http_error' => false
        ]);
    }

    /**
     * @return string
     * @throws GuzzleException
     */

    private function generateToken(): string
    {
        $request = $this->client()->post($this->base . 'Token', [
            'json' => [
                'UserApiKey' => $this->APIKey,
                'SecretKey' => $this->SecretKey,
            ],
        ]);

        if ($request->getStatusCode() != 201) {

            throw new Exception('Failed to get data from SMS.ir Api', $request->getStatusCode());

        }

        $request = json_decode($request->getBody()->getContents());

        if (!$request->IsSuccessful) {

            throw new Exception('Connection to SMS.ir Api was unsuccessful');

        }

        return $request->TokenKey;

    }

    /**
     * @param string $from
     * @param string $until
     * @param string $rows
     * @param string $pages
     * @return Collection
     * @throws GuzzleException
     */

    public function sentMessagesLogs(string $from, string $until, string $rows, string $pages): Collection
    {
        $request = $this->client(true)->get('https://RestfulSms.com/api/MessageSend', [
            'query' => [
                'Shamsi_FromDate' => $from,
                'Shamsi_ToDate' => $until,
                'RowsPerPage' => $rows,
                'RequestedPageNumber' => $pages
            ],
        ]);

        if ($request->getStatusCode() != 201) {

            throw new Exception('Failed to get data from SMS.ir Api', $request->getStatusCode());

        }

        $request = json_decode($request->getBody()->getContents());

        if (!$request->IsSuccessful) {

            throw new Exception('Connection to SMS.ir Api was unsuccessful');

        }

        return collect($request->Messages);
    }

    /**
     * @param array $data
     * @return bool
     * @throws GuzzleException
     */

    public function UltraFastSend(array $data): bool
    {
        $request = $this->client(true)->post('https://RestfulSms.com/api/UltraFastSend', [
            'json' => $data,
        ]);

        if ($request->getStatusCode() != 201) {

            throw new Exception('Failed to get data from SMS.ir Api', $request->getStatusCode());

        }

        $request = json_decode($request->getBody()->getContents());

        if (!$request->IsSuccessful) {

            throw new Exception('Connection to SMS.ir Api was unsuccessful');

        }

        return true;

    }
}
