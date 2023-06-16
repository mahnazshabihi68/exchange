<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @var int
     */
    public const PAGINATION_CHUNK = 20;

    /*
    |--------------------------------------------------------------------------
    | Setters and Getters
    |--------------------------------------------------------------------------
    */
    /**
     * @return Attribute
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: static fn($value) => $value ? Date::toCarbonGregorianFormat($value, 'Y-m-d H:i:s') : null,
        );
    }

    /**
     * @return Attribute
     */
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: static fn($value) => $value ? Date::toCarbonGregorianFormat($value, 'Y-m-d H:i:s') : null,
        );
    }
}
