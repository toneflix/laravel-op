<?php

namespace App\Models\v1;

use App\Services\AppInfo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use ToneflixCode\LaravelFileable\Media;

class File extends Model
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
        'meta' => 'array',
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
        'file_url',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            if ($item->fileable instanceof Movie) {
                $item->file = (new Media)->save('private.movies', 'file', $item->file);
            } elseif ($item->fileable instanceof Music) {
                $item->file = (new Media)->save('private.music', 'file', $item->file);
            } elseif ($item->fileable instanceof Nft) {
                $item->file = (new Media)->save('private.files', 'file', $item->file);
            } else {
                $item->file = (new Media)->save('default', 'file', $item->file);
            }
            if (! $item->file) {
                unset($item->file);
            }
            if (! $item->meta) {
                unset($item->meta);
            }
        });

        static::deleted(function ($item) {
            if ($item->fileable instanceof Movie) {
                $item->file = (new Media)->delete('private.movies', $item->file);
            } elseif ($item->fileable instanceof Music) {
                $item->file = (new Media)->delete('private.music', $item->file);
            } elseif ($item->fileable instanceof Nft) {
                $item->file = (new Media)->delete('private.files', $item->file);
            } else {
                $item->file = (new Media)->delete('default', $item->file);
            }
        });
    }

    /**
     * Get the parent fileable model (album or vision board).
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    /**
     * Get posibly protected URL of the image.
     *
     * @return string
     */
    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // $wt = config('app.env') === 'local' ? '?wt='.Auth::user()->window_token : '?ctx='.rand();
                if (! $this->fileable instanceof Movie &&
                ! $this->fileable instanceof Music &&
                ! $this->fileable instanceof Nft) {
                    return (new Media)->getMedia('default', $this->file);
                }

                $wt = '?preload=true';

                $superLoad = Auth::user()->role === 'admin';

                if ($superLoad) {
                    $wt = '?preload=true&wt=' . (Auth::user()->window_token ?? rand());
                } elseif ($this->fileable && $this->fileable->user->id === Auth::user()->id) {
                    $wt = '?preload=true&wt=' . ($this->fileable->user->window_token ?? rand());
                }

                $wt .= '&ctx='.rand();
                $wt .= '&build='.AppInfo::basic()['version'] ?? '1.0.0';
                $wt .= '&mode='.config('app.env');
                $wt .= '&pov='.md5($this->file);


                if ($this->fileable instanceof Movie) {
                    return (new Media)->getMedia('private.movies', $this->file) . $wt;
                } elseif ($this->fileable instanceof Music) {
                    return (new Media)->getMedia('private.music', $this->file) . $wt;
                } else {
                    return (new Media)->getMedia('private.files', $this->file) . $wt;
                }
            },
        );
    }

    /**
     * Get a shared/public URL of the image.
     *
     * @return string
     */
    protected function sharedFileUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // $wt = config('app.env') === 'local' ? '?wt='.Auth::user()->window_token : '?ctx='.rand();
                if (! $this->fileable instanceof Movie &&
                ! $this->fileable instanceof Music &&
                ! $this->fileable instanceof Nft) {
                    return (new Media)->getMedia('default', $this->file);
                }

                $wt = '?preload=true&shared&wt='.Auth::user()->window_token;
                $wt .= '&ctx='.rand();
                $wt .= '&build='.AppInfo::basic()['version'] ?? '1.0.0';
                $wt .= '&mode='.config('app.env');
                $wt .= '&pov='.md5($this->file);

                if ($this->fileable instanceof Movie) {
                    return (new Media)->getMedia('private.movies', $this->file) . $wt;
                } elseif ($this->fileable instanceof Music) {
                    return (new Media)->getMedia('private.music', $this->file) . $wt;
                } else {
                    return (new Media)->getMedia('private.files', $this->file) . $wt;
                }
            },
        );
    }
}