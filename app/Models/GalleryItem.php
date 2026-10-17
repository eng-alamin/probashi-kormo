<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GalleryCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'caption',
        'image_path',
        'is_active',
        'sort_order',
        'category',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => GalleryCategory::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<GalleryItem>  $query
     * @return Builder<GalleryItem>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<GalleryItem>  $query
     * @return Builder<GalleryItem>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }
}
