<?php

namespace App\Models\v1\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class HomepageService extends Model
{
    use HasFactory, Fileable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'icon',
        'type',
        'template',
    ];

    /**
     * Get the page that owns the HomepageService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent_item(): BelongsTo
    {
        return $this->belongsTo(Homepage::class, 'parent');
    }

    public function registerFileable(): void
    {
        $this->fileableLoader([
            'image' => 'default',
            'image2' => 'default',
        ]);
    }

    public static function registerEvents()
    {
        static::creating(function ($item) {
            $slug = str($item->title)->slug();
            $item->slug = (string) Homepage::whereSlug($slug)->exists() ? $slug->append(rand()) : $slug;
        });
    }

    /**
     * Scope this content to it's parent
     *
     * @param [type] $query
     * @param [type] $parent_id
     * @return void
     */
    public function scopeParent($query, $parent_id)
    {
        $query->where('parent', $parent_id);
    }

    /**
     * Scope this content to it's parent
     *
     * @param [type] $query
     * @param [type] $parent_id
     * @return void
     */
    public function scopeisType($query, $type)
    {
        $query->where('type', $type);
    }
}
