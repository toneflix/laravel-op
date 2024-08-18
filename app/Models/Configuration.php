<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @method static Model<Configuration> notSecret()
 */
class Configuration extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes to be appended
     *
     * @var array
     */
    protected $appends = [
        'multiple',
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

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'col' => 12,
        'max' => null,
        'hint' => '',
        'type' => 'string',
        'count' => null,
        'group' => 'main',
        'secret' => false,
        'choices' => "[]",
        'autogrow' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => \App\Casts\ConfigType::class,
            'value' => \App\Casts\ConfigValue::class,
            'secret' => 'boolean',
            'autogrow' => 'boolean',
            'choices' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        self::saved(function () {
            Cache::forget('configuration::build');
        });
    }

    /**
     * Set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array<string, mixed>|string|null  $key
     * @param mixed $value
     * @param boolean $loadSecret
     * @return  \Illuminate\Support\Collection
     */
    public static function set(
        string|array|null $key = null,
        mixed $value = null,
        bool $loadSecret = false
    ) {
        if (is_array($key)) {
            foreach ($key as $k => $value) {
                if ($value !== '***********') {
                    Configuration::where('key', $k)->update(['value' => $value]);
                }
            }
        } else {
            if ($value !== '***********') {
                Configuration::where('key', $key)->update(['value' => $value]);
            }
        }

        Cache::forget('configuration::build');

        return Configuration::build($loadSecret);
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
            return Configuration::all()->filter(fn($conf) => !$conf->secret)->mapWithKeys(function ($item) {
                return [$item->key => $item->value];
            });
        });

        return $config;
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function scopeNotSecret(Builder $query, $secret = false): void
    {
        $query->whereSecret($secret);
    }

    public function multiple(): Attribute
    {
        return new Attribute(
            get: fn() => count($this->choices) && $this->autogrow,
            set: fn($value) => [
                'autogrow' => $value
            ],
        );
    }
}
