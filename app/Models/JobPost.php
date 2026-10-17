<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'country_id',
        'title',
        'slug',
        'summary',
        'description',
        'requirements',
        'benefits',
        'salary_min',
        'salary_max',
        'salary_currency',
        'vacancies',
        'deadline',
        'is_featured',
        'is_active',
        'category',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'salary_min' => 'integer',
            'salary_max' => 'integer',
            'vacancies' => 'integer',
            'deadline' => 'date',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return HasMany<JobApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Active and not past its deadline (no deadline means always open).
     *
     * @param  Builder<JobPost>  $query
     * @return Builder<JobPost>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->whereNull('deadline')->orWhere('deadline', '>=', today()->toDateString());
            });
    }

    /**
     * @param  Builder<JobPost>  $query
     * @return Builder<JobPost>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
