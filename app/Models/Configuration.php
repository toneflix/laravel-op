<?php

namespace App\Models;

use App\Casts\ConfigValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => ConfigValue::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'value',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::saved(function () {
            Cache::forget('configuration::build');
        });
    }

    public static function build()
    {
        /** @var \Illuminate\Support\Collection<TMapWithKeysKey, TMapWithKeysValue> $config */
        $config = Cache::remember('configuration::build', null, function () {
            return Configuration::all()->mapWithKeys(function ($item) {
                return [$item->key => $item->value];
            });
        });

        return $config;
    }

    public function files()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
