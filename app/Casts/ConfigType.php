<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ConfigType implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (request()->isMethod('POST') || request()->isMethod('PUT')) {
            return match (true) {
                $value === 'text' => 'string',
                $value === 'bool' => 'boolean',
                $value === 'files' => 'file',
                $value === 'json' => 'array',
                in_array($value, ['number', 'integer', 'int']) => 'numeric',
                default => $value,
            };
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
