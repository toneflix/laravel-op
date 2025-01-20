<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;
use ToneflixCode\DbConfig\Models\Fileable as ModelsFileable;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class File extends ModelsFileable
{
    use Fileable;

    /**
     * Map fileable to a collection
     *
     * @var array<class-string, string>
     *
     * @example $mediaPaths [User::class => 'avatar', Configuration::class => 'default']
     */
    public $mediaPaths = [
        User::class => 'avatar',
    ];

    public function registerFileable(): void
    {
        $this->fileableLoader(
            file_field: 'file',
            collection: $this->fileable_collection ?? $this->mediaPaths[$this->fileable_type] ?? 'default',
            applyDefault: false,
        );
    }

    public function progress(): Attribute
    {
        return new Attribute(
            get: function () {
                $diskName = 'streamable_media';

                $meta_type = isset($this->meta['type']) ? ".{$this->meta['type']}" : '';

                $mediaPath = $this->mediaPaths[$this->fileable_type.$meta_type] ?? 'default';

                if ($mediaPath === 'private.music') {
                    $diskName = 'gpaf_media';
                }

                $progress = 0;

                if ($this->fileable->processed_at) {
                    return 100;
                } elseif ($diskName === 'gpaf_media') {
                    $progress = Cache::get('media.segment.'.str($this->file.'.'.$this->id)->toString(), 0);
                }

                return $progress;
            },
        );
    }

    public function progressComplete(): Attribute
    {
        return new Attribute(
            get: fn () => $this->progress >= 100 ? '100% completed!' : "{$this->progress}% processing...",
        );
    }

    /**
     * Get a shared/public URL of the image.
     *
     * @return string
     */
    protected function sharedUrl(): Attribute
    {
        $link = $this->get_files['file']['secureLink'];
        if (($this->mediaPaths[$this->fileable_type] ?? 'default') === 'default') {
            $link = $this->get_files['file']['dynamicLink'];
        }

        return Attribute::make(get: fn () => $link);
    }
}
