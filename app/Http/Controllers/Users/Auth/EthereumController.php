<?php

namespace App\Http\Controllers\Users\Auth;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Ethereum\VerifyMessage;
use App\Models\Otp;
use App\Models\User;
use Elliptic\EC;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use kornrunner\Keccak;

class EthereumController extends Controller
{
    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function getMessage(): JsonResponse
    {
        try {

            /**
             * Generate nonce.
             */

            $nonce = Str::orderedUuid()->toString();

            /**
             * Generate new random string.
             */

            $message = 'I have read and accept the terms and conditions.\nPlease sign me in.\n\nSecurity code (you can ignore this): ' . $nonce;

            /**
             * Store message and nonce.
             */

            Redis::set('ethereum-nonce:' . $nonce, $message);

            /**
             * Return response.
             */

            return response()->json([
                'message' =>  $message,
                'nonce' =>  $nonce
            ]);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param VerifyMessage $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function verifyMessage(VerifyMessage $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Fetch nonce.
             */

            $message = Redis::get('ethereum-nonce:' . $request->nonce);

            if (!$message){

                throw new Exception(__('messages.auth.login.failed'));

            }

            /**
             * Verify signature and address.
             */

            if (!$this->verifySignature($message, $request->signature, $request->address)){

                throw new Exception(__('messages.auth.login.failed'));

            }

            /**
             * Delete nonce.
             */

            Redis::del('ethereum-nonce:' . $request->nonce);

            /**
             * Login user.
             */

            $user = User::firstOrCreate([
                'eth_address'   =>  $request->address
            ]);

            /**
             * Create token.
             */

            $accessToken =  $user->createToken('ethereum');

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message'   =>  __('messages.auth.login.successful'),
                'access-token'  =>  $accessToken->plainTextToken,
                'user'  =>  $user
            ]);

        } catch (Exception $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param $message
     * @param $signature
     * @param $address
     * @return bool
     * @throws Exception
     */

    protected function verifySignature($message, $signature, $address): bool
    {
        $hash = Keccak::hash(sprintf("\x19Ethereum Signed Message:\n%s%s", strlen($message), $message), 256);

        $sign = [
            "r" => substr($signature, 2, 64),
            "s" => substr($signature, 66, 64)
        ];

        $recId = ord(hex2bin(substr($signature, 130, 2))) - 27;

        if ($recId !== ($recId & 1)) {

            return false;

        }

        $publicKey = (new EC('secp256k1'))->recoverPubKey($hash, $sign, $recId);

        if (
            Str::of($address)->after('0x')->lower()
            !=
            substr(Keccak::hash(substr(hex2bin($publicKey->encode('hex')), 1), 256), 24)
        ){

            return false;
        }

        return true;
    }
}
