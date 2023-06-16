<?php

namespace App\Webservices\AtipayWithdraw\impls;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Webservices\AtipayWithdraw\interfaces\IAtipayWithdrawService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AtipayWithdrawService implements IAtipayWithdrawService
{
    /**
     * @var string
     */

    protected string $base;

    /**
     * @var string
     */

    protected string $username;

    /**
     * @var string
     */

    protected string $password;

    public function __construct()
    {
        $this->base = Config::get('app.atipay_base_url');

        $this->username = Config::get('app.atipay_withdraw_username');

        $this->password = Config::get('app.atipay_withdraw_password');
    }


    /**
     * @param array $data
     * @return Collection|\Tightenco\Collect\Support\Collection
     * @throws GuzzleException
     * @throws Exception
     */
    public function directTransfer(array $data)
    {
        $result = collect(
            json_decode(
                $this->client()->post('normal-transfer', [
                    'json' => [
                        'amount' => $data['amount'],
                        'destinationDeposit' => $data['destinationDeposit']
                    ],
                ])->getBody()->getContents()
            )
        );

        if (!$result->has('refernceNumber')) {
            throw new Exception(Lang::get('messages.failed'));
        }

        return $result;
    }

    /**
     * @param array $data
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */
    public function payaTransfer(array $data): Collection
    {
        return collect(
            json_decode(
                $this->client()->post('ach-transfer', [
                    'json' => [
                        'amount' => $data['amount'],
                        'description' => $data['description'],
                        'ibanNumber' => $data['ibanNumber'],
                        'ownerName' => $data['ownerName'],
                        'transferDescription' => $data['transferDescription'],
                        'factorNumber' => $data['factorNumber']
                    ],
                ])->getBody()->getContents()
                ,
                true
            )
        );
    }

    /**
     * @param Model $data
     * @return Collection
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function payaTransferReport(Model $data): Collection
    {
        $data = $this->client()->post('ach-report', [
            'json' => [
                'destinationIbanNumber' => $data['destination_iban_number'],
                'referenceId' => $data['reference_id'],
                'length' => 1
            ],
        ]);
        $dataCollection = collect(
            json_decode(
                $data->getBody()->getContents()
            )
        );

        if (isset($dataCollection['data'])) {
            return $dataCollection;
        } else {
            Logger::error($dataCollection['faErrorMessage'], Util::jsonEncodeUnicode($dataCollection['faErrorMessage']));
            throw new NotFoundHttpException($dataCollection['faErrorMessage']);
        }
    }


    /**
     * @param array $data
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */
    public
    function satnaTransfer(
        array $data
    ): Collection {
        $result = collect(
            json_decode(
                $this->client()->post('rtgs-transfer', [
                    'json' => [
                        'amount' => $data['amount'],
                        'description' => $data['description'],
                        'destinationIbanNumber' => $data['destinationIbanNumber'],
                        'factorNumber' => $data['factorNumber']
                    ],
                ])->getBody()->getContents()
            )
        );

        if (!$result->has('id')) {
            throw new Exception(__('messages.failed'));
        }

        return $result;
    }

    /**
     * @return Client
     */
    private function client(): Client
    {
        return new Client([
            'base_uri' => $this->base,
            'http_errors' => false,
            'headers' => [
                'accept' => 'application/json',
                'Content-type' => 'application/json',
            ],
            'auth' => [$this->username, $this->password]
        ]);
    }
}
