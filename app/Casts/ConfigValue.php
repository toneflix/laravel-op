<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use ToneflixCode\LaravelFileable\Media;

class ConfigValue implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $this->build($value, $attributes['type'], $model);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return match (true) {
            in_array(mb_strtolower($attributes['type']), ['json', 'array']) => json_encode($value, JSON_FORCE_OBJECT),
            default => $value,
        };
    }

    protected function build(mixed $value, string $type, Model $model): mixed
    {
        return match (true) {
            $type === 'file' => $model->image_url ?? (new Media())->getDefaultMedia('default'),
            $type === 'files' => $model->files,
            in_array(mb_strtolower($type), ['bool', 'boolean']) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            in_array(mb_strtolower($type), ['json', 'array']) => json_decode($value, true),
            in_array(mb_strtolower($type), ['number', 'integer', 'int']) => (int) $value,
            default => $value,
        };
    }
}
