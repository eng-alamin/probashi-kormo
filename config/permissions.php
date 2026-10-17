<?php

/*
 * Every admin permission is "module.action". RoleSeeder/PermissionSeeder create them.
 * Super Admin passes every check (Gate::before). Admin and Staff get the defaults below;
 * the Roles page (Step 07E) can change them later.
 */
return [
    'modules' => [
        'applications' => ['view', 'update', 'delete'],
        'inquiries' => ['view', 'update', 'delete'],
        'jobs' => ['view', 'create', 'update', 'delete'],
        'countries' => ['view', 'create', 'update', 'delete'],
        'trainings' => ['view', 'create', 'update', 'delete'],
        'services' => ['view', 'create', 'update', 'delete'],
        'gallery' => ['view', 'create', 'update', 'delete'],
        'news' => ['view', 'create', 'update', 'delete'],
        'stories' => ['view', 'create', 'update', 'delete'],
        'users' => ['view', 'create', 'update', 'delete'],
        'roles' => ['view', 'create', 'update', 'delete'],
        'logs' => ['view'],
        'settings' => ['view', 'update'],
    ],

    // Admin gets everything except these modules.
    // Names and action words shown on the Roles & Permissions page.
    'labels' => [
        'applications' => 'Applications',
        'inquiries' => 'Inquiries',
        'jobs' => 'Jobs',
        'countries' => 'Countries',
        'trainings' => 'Training',
        'services' => 'Services',
        'gallery' => 'Gallery',
        'news' => 'News & Notice',
        'stories' => 'Success Stories',
        'users' => 'Staff Users',
        'roles' => 'Roles & Permissions',
        'logs' => 'Activity Log',
        'settings' => 'Site Settings',
    ],

    'action_labels' => [
        'view' => 'View',
        'create' => 'Add',
        'update' => 'Edit',
        'delete' => 'Delete',
    ],

    'admin_excluded_modules' => ['users', 'roles', 'logs'],

    // Staff gets only these.
    'staff' => [
        'applications.view',
        'applications.update',
        'inquiries.view',
        'inquiries.update',
    ],
];
