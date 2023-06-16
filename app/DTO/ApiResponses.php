<?php

namespace App\DTO;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponses
{
    public static function jsonResponse($data, int $statusCode)
    {
        return response()->json($data, $statusCode);
    }

    public static function successResponse($data = null, $message = '', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $data['status'] = $statusCode;
        $data['message'] = $message ? $message : __('messages.kyc.status-code.' . "$statusCode");

        return self::jsonResponse($data, $statusCode);
    }

    public static function errorResponse($message, int $statusCode = Response::HTTP_NOT_FOUND): JsonResponse
    {
        $message = $message ? : __('messages.kyc.status-code.' . "$statusCode");

        $data = [
            'status' => $statusCode,
            'message' => $message,
        ];

        return self::jsonResponse($data, $statusCode);
    }
}
