<?php

namespace App\Webservices\KycAuthorization\impls;

use App\Webservices\KycAuthorization\interfaces\IKycAuthorizationService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class KycAuthorizationService implements IKycAuthorizationService
{
    /**
     * @var string
     */
    protected string $main;

    /**
     * @var string
     */
    protected string $secretKey;

    /**
     * @var string
     */
    protected string $id;

    public function __construct()
    {
        $this->main = Config::get('app.kyc_base_url');
        $this->secretKey = Config::get('app.kyc_secret_key');
        $this->id = Config::get('app.kyc_id');
    }

    /**
     * @return Collection
     * @throws GuzzleException
     */
    public function getAccessToken(): Collection
    {
        # get access token for get user info with id and secretKey
        return collect(json_decode($this->client()->post('business/login', [
            'json' => [
                'id' => $this->id,
                'secretKey' => $this->secretKey
            ]
        ])->getBody()->getContents()));
    }

    /**
     * @param $data
     * @return Collection
     * @throws GuzzleException
     */
    public function getAuthorizationKycInfo($data): Collection
    {
        if ($this->getAccessToken()['status'] == 200)
            $accessToken = $this->getAccessToken()['message']->oauthInformation->access_token;
        else
            return collect($this->getAccessToken());

        # get user info with client token
        return collect(json_decode($this->client($accessToken)->post('getUserInfo', [
            'json' => [
                'clientToken' => $data['clientToken'],
            ],
        ])->getBody()->getContents()));
    }

    /**
     * @param $nationalCode
     * @return Collection
     * @throws GuzzleException
     */
    public function getClientTokenByNationalCode($nationalCode): Collection
    {
        if ($this->getAccessToken()['status'] == 200)
            $accessToken = $this->getAccessToken()['message']->oauthInformation->access_token;
        else
            return collect($this->getAccessToken());

        return collect(json_decode($this->client($accessToken)->get('clientToken/' . $nationalCode)->getBody()->getContents()));
    }

    /**
     * @param $token
     * @return Client
     */
    private function client($token = null): Client
    {
        $headers = [
            'accept' => 'application/json',
            'Content-type' => 'application/json;charset=UTF-8'
        ];

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return new Client([
            'base_uri' => $this->main,
            'headers' => $headers,
            'http_errors' => false
        ]);
    }
}
