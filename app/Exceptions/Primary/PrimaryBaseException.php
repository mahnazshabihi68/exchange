<?php

namespace App\Exceptions\Primary;

use App\Helpers\Logger;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class PrimaryBaseException extends Exception
{
    /**
     * @param  string  $exceptionMessage
     * @param  int  $exceptionStatusCode
     */
    public function __construct(protected string $exceptionMessage, protected int $exceptionStatusCode)
    {
        $translationExists = array_key_exists($this->exceptionMessage, Lang::get('messages.exceptions.primary'));
        $exceptionMessage = $translationExists ? Lang::get(
            'messages.exceptions.primary.'.$exceptionMessage
        ) : $exceptionMessage;
        $this->setExceptionMessage($exceptionMessage);

        parent::__construct($this->getExceptionMessage());
    }

    /**
     * @return int
     */
    public function getExceptionStatusCode(): int
    {
        return $this->exceptionStatusCode;
    }

    /**
     * @param  int  $exceptionStatusCode
     * @return PrimaryBaseException
     */
    public function setExceptionStatusCode(int $exceptionStatusCode): self
    {
        $this->exceptionStatusCode = $exceptionStatusCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getExceptionMessage(): string
    {
        return $this->exceptionMessage;
    }

    /**
     * @param  string  $exceptionMessage
     * @return PrimaryBaseException
     */
    public function setExceptionMessage(string $exceptionMessage): self
    {
        $this->exceptionMessage = $exceptionMessage;
        return $this;
    }

    /**
     * @return bool
     */
    public function report(): bool
    {
        Logger::error(
            message: $this->getExceptionMessage(),
            data: [
                'status_code' => $this->getExceptionStatusCode(),
                'message' => $this->getExceptionMessage(),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
            ]
        );
        return false;
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => $this->getExceptionMessage()
        ], $this->getExceptionStatusCode());
    }
}
