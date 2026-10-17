<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\PassportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'job_post_id',
        'country_id',
        'full_name',
        'phone',
        'age',
        'district',
        'passport_status',
        'education',
        'experience',
        'message',
        'status',
        'admin_note',
        'handled_by',
        'trade',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'passport_status' => PassportStatus::class,
            'status' => ApplicationStatus::class,
        ];
    }

    /**
     * @return BelongsTo<JobPost, $this>
     */
    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
