<?php

namespace App\Helpers;

class Util
{
    /**
     * @param  array  $data
     * @return false|string
     * @throws \JsonException
     */
    public static function jsonEncodeUnicode(mixed $data): bool|string
    {
        return json_encode(
            $data,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

}
