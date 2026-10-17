<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\EnsureAdminAccess;
use App\Livewire\Admin\Applications as AdminApplications;
use App\Livewire\Admin\Countries as AdminCountries;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Gallery as AdminGallery;
use App\Livewire\Admin\Inquiries as AdminInquiries;
use App\Livewire\Admin\Jobs as AdminJobs;
use App\Livewire\Admin\Logs as AdminLogs;
use App\Livewire\Admin\News as AdminNews;
use App\Livewire\Admin\Roles as AdminRoles;
use App\Livewire\Admin\Services as AdminServices;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Admin\Stories as AdminStories;
use App\Livewire\Admin\Trainings as AdminTrainings;
use App\Livewire\Admin\Users as AdminUsers;
use App\Livewire\Auth\Login;
use App\Livewire\Pages\About;
use App\Livewire\Pages\Apply;
use App\Livewire\Pages\Contact;
use App\Livewire\Pages\Countries;
use App\Livewire\Pages\Gallery;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Jobs;
use App\Livewire\Pages\News;
use App\Livewire\Pages\PrivacyPolicy;
use App\Livewire\Pages\Services;
use App\Livewire\Pages\SuccessStories;
use App\Livewire\Pages\Terms;
use App\Livewire\Pages\Training;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::livewire('/', Home::class)->name('home');

Route::livewire('/services', Services\Index::class)->name('services');
Route::livewire('/services/{service}', Services\Show::class)->name('services.show');

Route::livewire('/countries', Countries\Index::class)->name('countries');
Route::livewire('/countries/{country}', Countries\Show::class)->name('countries.show');

Route::livewire('/training', Training\Index::class)->name('training');
Route::livewire('/training/{course}', Training\Show::class)->name('training.show');

Route::livewire('/jobs', Jobs\Index::class)->name('jobs');
Route::livewire('/jobs/{job}', Jobs\Show::class)->name('jobs.show');

Route::livewire('/gallery', Gallery::class)->name('gallery');
Route::livewire('/success-stories', SuccessStories::class)->name('success-stories');

Route::livewire('/news', News\Index::class)->name('news');
Route::livewire('/news/{post}', News\Show::class)->name('news.show');

Route::livewire('/apply', Apply::class)->name('apply');
Route::livewire('/contact', Contact::class)->name('contact');

Route::livewire('/about', About::class)->name('about');
Route::livewire('/terms', Terms::class)->name('terms');
Route::livewire('/privacy-policy', PrivacyPolicy::class)->name('privacy-policy');

/*
|--------------------------------------------------------------------------
| SEO (public/robots.txt must not exist, or the file wins over this route)
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Admin authentication
|--------------------------------------------------------------------------
*/
Route::livewire('/admin/login', Login::class)->name('login');
Route::post('/admin/logout', LogoutController::class)->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin panel (login + staff role required)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureAdminAccess::class])
    ->group(function (): void {
        Route::livewire('/', Dashboard::class)->name('dashboard');

        Route::livewire('/applications', AdminApplications\Index::class)
            ->middleware('can:applications.view')->name('applications.index');
        Route::livewire('/applications/{application}', AdminApplications\Show::class)
            ->middleware('can:applications.view')->name('applications.show');

        Route::livewire('/inquiries', AdminInquiries\Index::class)
            ->middleware('can:inquiries.view')->name('inquiries.index');
        Route::livewire('/inquiries/{inquiry}', AdminInquiries\Show::class)
            ->middleware('can:inquiries.view')->name('inquiries.show');

        // "create" is declared before "{job}/edit" style routes; the two never clash.
        Route::livewire('/jobs', AdminJobs\Index::class)
            ->middleware('can:jobs.view')->name('jobs.index');
        Route::livewire('/jobs/create', AdminJobs\Form::class)
            ->middleware('can:jobs.create')->name('jobs.create');
        Route::livewire('/jobs/{job}/edit', AdminJobs\Form::class)
            ->middleware('can:jobs.update')->name('jobs.edit');

        Route::livewire('/countries', AdminCountries\Index::class)
            ->middleware('can:countries.view')->name('countries.index');
        Route::livewire('/countries/create', AdminCountries\Form::class)
            ->middleware('can:countries.create')->name('countries.create');
        Route::livewire('/countries/{country}/edit', AdminCountries\Form::class)
            ->middleware('can:countries.update')->name('countries.edit');

        Route::livewire('/trainings', AdminTrainings\Index::class)
            ->middleware('can:trainings.view')->name('trainings.index');
        Route::livewire('/trainings/create', AdminTrainings\Form::class)
            ->middleware('can:trainings.create')->name('trainings.create');
        Route::livewire('/trainings/{course}/edit', AdminTrainings\Form::class)
            ->middleware('can:trainings.update')->name('trainings.edit');

        Route::livewire('/services', AdminServices\Index::class)
            ->middleware('can:services.view')->name('services.index');
        Route::livewire('/services/create', AdminServices\Form::class)
            ->middleware('can:services.create')->name('services.create');
        Route::livewire('/services/{service}/edit', AdminServices\Form::class)
            ->middleware('can:services.update')->name('services.edit');

        Route::livewire('/gallery', AdminGallery\Index::class)
            ->middleware('can:gallery.view')->name('gallery.index');
        Route::livewire('/gallery/create', AdminGallery\Form::class)
            ->middleware('can:gallery.create')->name('gallery.create');
        Route::livewire('/gallery/{item}/edit', AdminGallery\Form::class)
            ->middleware('can:gallery.update')->name('gallery.edit');

        Route::livewire('/news', AdminNews\Index::class)
            ->middleware('can:news.view')->name('news.index');
        Route::livewire('/news/create', AdminNews\Form::class)
            ->middleware('can:news.create')->name('news.create');
        Route::livewire('/news/{post}/edit', AdminNews\Form::class)
            ->middleware('can:news.update')->name('news.edit');

        Route::livewire('/stories', AdminStories\Index::class)
            ->middleware('can:stories.view')->name('stories.index');
        Route::livewire('/stories/create', AdminStories\Form::class)
            ->middleware('can:stories.create')->name('stories.create');
        Route::livewire('/stories/{record}/edit', AdminStories\Form::class)
            ->middleware('can:stories.update')->name('stories.edit');

        Route::livewire('/users', AdminUsers\Index::class)
            ->middleware('can:users.view')->name('users.index');
        Route::livewire('/users/create', AdminUsers\Form::class)
            ->middleware('can:users.create')->name('users.create');
        Route::livewire('/users/{user}/edit', AdminUsers\Form::class)
            ->middleware('can:users.update')->name('users.edit');

        Route::livewire('/roles', AdminRoles\Index::class)
            ->middleware('can:roles.view')->name('roles.index');
        Route::livewire('/roles/{role}/edit', AdminRoles\Form::class)
            ->middleware('can:roles.update')->name('roles.edit');

        Route::livewire('/logs', AdminLogs\Index::class)
            ->middleware('can:logs.view')->name('logs.index');

        Route::livewire('/settings', AdminSettings\Index::class)
            ->middleware('can:settings.view')->name('settings');
    });
