<?php

/*
 * Admin sidebar. Each item points to a named route.
 * - "match": route pattern used to highlight the active item.
 * - "can": permission needed to see the item (hidden otherwise).
 * - "tab": true = also shown in the mobile bottom bar.
 * Items whose route does not exist yet are shown as "Soon" and stay disabled.
 */
return [
    'sections' => [
        [
            'label' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'tab' => true],
            ],
        ],
        [
            'label' => 'Recruitment',
            'items' => [
                ['label' => 'Applications', 'icon' => 'bi-person-lines-fill', 'route' => 'admin.applications.index', 'can' => 'applications.view', 'match' => 'admin.applications.*', 'tab' => true],
                ['label' => 'Jobs', 'icon' => 'bi-briefcase-fill', 'route' => 'admin.jobs.index', 'can' => 'jobs.view', 'match' => 'admin.jobs.*', 'tab' => true],
                ['label' => 'Countries', 'icon' => 'bi-globe2', 'route' => 'admin.countries.index', 'can' => 'countries.view', 'match' => 'admin.countries.*'],
                ['label' => 'Training', 'icon' => 'bi-mortarboard-fill', 'route' => 'admin.trainings.index', 'can' => 'trainings.view', 'match' => 'admin.trainings.*'],
                ['label' => 'Services', 'icon' => 'bi-tools', 'route' => 'admin.services.index', 'can' => 'services.view', 'match' => 'admin.services.*'],
            ],
        ],
        [
            'label' => 'Communication',
            'items' => [
                ['label' => 'Inquiries', 'icon' => 'bi-inbox-fill', 'route' => 'admin.inquiries.index', 'can' => 'inquiries.view', 'match' => 'admin.inquiries.*', 'tab' => true],
            ],
        ],
        [
            'label' => 'Website Content',
            'items' => [
                ['label' => 'Gallery', 'icon' => 'bi-images', 'route' => 'admin.gallery.index', 'can' => 'gallery.view', 'match' => 'admin.gallery.*'],
                ['label' => 'News & Notice', 'icon' => 'bi-megaphone-fill', 'route' => 'admin.news.index', 'can' => 'news.view', 'match' => 'admin.news.*'],
                ['label' => 'Success Stories', 'icon' => 'bi-trophy-fill', 'route' => 'admin.stories.index', 'can' => 'stories.view', 'match' => 'admin.stories.*'],
            ],
        ],
        [
            'label' => 'Access',
            'items' => [
                ['label' => 'Staff Users', 'icon' => 'bi-people-fill', 'route' => 'admin.users.index', 'can' => 'users.view', 'match' => 'admin.users.*'],
                ['label' => 'Roles & Permissions', 'icon' => 'bi-shield-lock-fill', 'route' => 'admin.roles.index', 'can' => 'roles.view', 'match' => 'admin.roles.*'],
            ],
        ],
        [
            'label' => 'System',
            'items' => [
                ['label' => 'Activity Log', 'icon' => 'bi-terminal-fill', 'route' => 'admin.logs.index', 'can' => 'logs.view', 'match' => 'admin.logs.*'],
                ['label' => 'Settings', 'icon' => 'bi-gear-fill', 'route' => 'admin.settings', 'can' => 'settings.view', 'match' => 'admin.settings', 'tab' => true],
            ],
        ],
    ],
];
