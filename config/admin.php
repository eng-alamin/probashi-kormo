<?php

return [
    /*
    | The first Super Admin account, created by "php artisan db:seed".
    | Set these in .env. After seeding you may delete ADMIN_PASSWORD from .env.
    */
    'super_admin' => [
        'name' => env('ADMIN_NAME', 'Super Admin'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
