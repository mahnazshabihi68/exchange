<?php

namespace App\Models;

use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class LogToMongoDB
 *
 * @property string $channel
 * @property string $level
 * @property string $data
 *
 * @property Carbon $created_at
 *
 * @method static create(array $toArray)
 *
 * @package App\Models
 */
class LogMongoDB extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mongodb';
    /**
     * @var string
     */
    protected $collection = 'logs';
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
}
