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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => ConfigValue::class,
            'secret' => 'boolean',
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'value',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'string',
        'count' => null,
        'max' => null,
        'col' => 12,
        'autogrow' => false,
        'hint' => '',
        'secret' => false,
    ];

    public static function boot(): void
    {
        parent::boot();

        self::saved(function () {
            Cache::forget('configuration::build');
        });
    }

    public static function build($loadSecret = false)
    {
        if ($loadSecret) {
            return Configuration::all()->mapWithKeys(function ($item) {
                return [$item->key => $item->value];
            });
        }

        /** @var \Illuminate\Support\Collection<TMapWithKeysKey, TMapWithKeysValue> $config */
        $config = Cache::remember('configuration::build', null, function () {
            return Configuration::all()->filter(fn ($conf) => !$conf->secret)->mapWithKeys(function ($item) {
                return [$item->key => $item->value];
            });
        });

        return $config;
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
