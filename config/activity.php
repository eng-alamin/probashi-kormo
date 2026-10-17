<?php

use App\Models\Country;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\NewsPost;
use App\Models\Service;
use App\Models\SuccessStory;
use App\Models\TrainingCourse;
use App\Models\User;

/*
 * Activity log settings.
 *
 * "models" lists every model whose create / update / delete is written to the log.
 *   - type:   short key saved in activity_logs.subject_type
 *   - label:  the word used in the sentence ("Created job ...")
 *   - name:   the column shown as the record's name
 *   - ignore: columns whose change alone is not worth a log line
 *
 * Only the NAMES of changed columns are logged, never their values, so phone numbers,
 * notes and passwords never end up in the log.
 */
return [
    // Older lines are deleted by "php artisan activity:prune" (scheduled daily).
    'retention_days' => (int) env('ACTIVITY_LOG_DAYS', 180),

    'models' => [
        JobApplication::class => ['type' => 'application', 'label' => 'application', 'name' => 'full_name', 'ignore' => ['handled_by']],
        Inquiry::class => ['type' => 'inquiry', 'label' => 'inquiry', 'name' => 'full_name', 'ignore' => ['read_at']],
        JobPost::class => ['type' => 'job', 'label' => 'job', 'name' => 'title'],
        Country::class => ['type' => 'country', 'label' => 'country', 'name' => 'name'],
        TrainingCourse::class => ['type' => 'training', 'label' => 'training course', 'name' => 'title'],
        Service::class => ['type' => 'service', 'label' => 'service', 'name' => 'title'],
        GalleryItem::class => ['type' => 'gallery', 'label' => 'gallery item', 'name' => 'title'],
        NewsPost::class => ['type' => 'news', 'label' => 'news post', 'name' => 'title'],
        SuccessStory::class => ['type' => 'story', 'label' => 'success story', 'name' => 'name'],
        User::class => ['type' => 'user', 'label' => 'staff user', 'name' => 'name'],
    ],
];
