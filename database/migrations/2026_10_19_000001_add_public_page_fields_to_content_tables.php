<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Adds the fields the public pages need (found by reading the static site).
 * List-style fields (job_sectors, what_we_do ...) are plain text, one item per line.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table): void {
            $table->string('flag', 16)->nullable();
            $table->text('job_sectors')->nullable();
            $table->string('contract_info')->nullable();
            $table->text('extra_info')->nullable();
        });

        Schema::table('job_posts', function (Blueprint $table): void {
            $table->string('category', 100)->nullable();
        });

        Schema::table('job_applications', function (Blueprint $table): void {
            $table->string('trade', 150)->nullable();
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->text('what_we_do')->nullable();
            $table->text('what_to_bring')->nullable();
        });

        Schema::table('training_courses', function (Blueprint $table): void {
            $table->string('icon', 100)->nullable();
            $table->text('learn_points')->nullable();
            $table->text('career_points')->nullable();
            $table->text('eligibility')->nullable();
            $table->string('class_days', 100)->nullable();
            $table->string('certificate_info')->nullable();
        });

        Schema::table('gallery_items', function (Blueprint $table): void {
            $table->string('category', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table): void {
            $table->dropColumn(['flag', 'job_sectors', 'contract_info', 'extra_info']);
        });

        Schema::table('job_posts', function (Blueprint $table): void {
            $table->dropColumn('category');
        });

        Schema::table('job_applications', function (Blueprint $table): void {
            $table->dropColumn('trade');
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn(['what_we_do', 'what_to_bring']);
        });

        Schema::table('training_courses', function (Blueprint $table): void {
            $table->dropColumn(['icon', 'learn_points', 'career_points', 'eligibility', 'class_days', 'certificate_info']);
        });

        Schema::table('gallery_items', function (Blueprint $table): void {
            $table->dropColumn('category');
        });
    }
};
