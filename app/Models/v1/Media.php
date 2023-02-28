<?php

namespace App\Models\v1;

use App\Services\AppInfo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use ToneflixCode\LaravelFileable\Media as TMedia;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'meta',
        'file',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'collection',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'meta' => '{}',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
    ];

    protected $paths = [
        // Feed::class => 'feeds',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            if (isset($this->paths[$item->mediable_type])) {
                $item->file = (new TMedia)->save($this->paths[$item->mediable_type], 'file', $item->file);
            } else {
                $item->file = (new TMedia)->save('private.images', 'file', $item->file);
            }
            if (! $item->file) {
                unset($item->file);
            }
            if (! $item->meta) {
                unset($item->meta);
            }
        });

        static::deleted(function ($item) {
            if (isset($this->paths[$item->mediable_type])) {
                (new TMedia)->delete($this->paths[$item->mediable_type], $item->file);
            } else {
                (new TMedia)->delete('private.images', $item->file);
            }
        });
    }

    /**
     * Get the parent mediable model (album or vision board).
     */
    public function mediable()
    {
        return $this->morphTo();
    }

    /**
     * Get posibly protected URL of the media.
     *
     * @return string
     */
    protected function mediaUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // $wt = config('app.env') === 'local' ? '?wt='.Auth::user()->window_token : '?ctx='.rand();
                if (isset($this->paths[$this->mediable_type])) {
                    return (new TMedia)->getMedia($this->paths[$this->mediable_type], $this->file);
                }

                $wt = '?preload=true';

                $superLoad = (Auth::user() ? Auth::user()->role === 'admin' : false);

                if ($superLoad) {
                    $wt = '?preload=true&wt='.Auth::user()->window_token;
                } elseif ($this->mediable && $this->mediable->user->id === (Auth::user() ? Auth::user()->id : 0)) {
                    $wt = '?preload=true&wt='.$this->mediable->user->window_token;
                }

                $wt .= '&ctx='.rand();
                $wt .= '&build='.AppInfo::basic()['version'] ?? '1.0.0';
                $wt .= '&mode='.config('app.env');
                $wt .= '&pov='.md5($this->src);

                return (new TMedia)->getMedia('private.images', $this->file).$wt;
            },
        );
    }

    /**
     * Get a shared/public URL of the image.
     *
     * @return string
     */
    protected function sharedMediaUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // $wt = config('app.env') === 'local' ? '?wt='.Auth::user()->window_token : '?ctx='.rand();
                if (isset($this->paths[$this->mediable_type])) {
                    return (new TMedia)->getMedia($this->paths[$this->mediable_type], $this->file);
                }

                $wt = '?preload=true&shared&wt='.(Auth::user() ? Auth::user()->window_token : rand());
                $wt .= '&ctx='.rand();
                $wt .= '&build='.AppInfo::basic()['version'] ?? '1.0.0';
                $wt .= '&mode='.config('app.env');
                $wt .= '&pov='.md5($this->file);

                return (new TMedia)->getMedia('private.images', $this->file).$wt;
            },
        );
    }
}