<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminUserSeeder::class,
            CountrySeeder::class,
            ServiceSeeder::class,
            TrainingCourseSeeder::class,
            JobPostSeeder::class,
            NewsPostSeeder::class,
            SuccessStorySeeder::class,
            GalleryItemSeeder::class,
        ]);
    }
}
