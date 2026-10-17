<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\ApplicationStatus;
use App\Enums\PassportStatus;
use App\Livewire\Forms\ApplyForm;
use App\Models\Country;
use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Apply Online')]
class Apply extends Component
{
    private const MAX_PER_HOUR = 5;

    public ApplyForm $form;

    /** Set from ?job=<slug>. Locked so the browser cannot change it. */
    #[Locked]
    public ?int $jobPostId = null;

    public bool $sent = false;

    public ?string $reference = null;

    public function mount(): void
    {
        $slug = request()->query('job');

        if (! is_string($slug) || $slug === '') {
            return;
        }

        $job = JobPost::open()->with('country:id,name')->where('slug', $slug)->first();

        if ($job === null) {
            return;
        }

        $this->jobPostId = $job->id;
        $this->form->trade = $job->title;

        if ($job->country_id !== null) {
            $this->form->country_id = (string) $job->country_id;
        }
    }

    public function submit(): void
    {
        // Honeypot: pretend it worked, store nothing.
        if ($this->form->website !== '') {
            $this->finish(null);

            return;
        }

        $this->form->validate();

        $key = 'apply:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_PER_HOUR)) {
            throw ValidationException::withMessages([
                'form.phone' => 'Too many applications from this connection. Please try again later or call us.',
            ]);
        }

        $phone = $this->form->normalizedPhone();

        // Double click / resubmit: same phone + same job within a day is not stored twice.
        $existing = JobApplication::query()
            ->where('phone', $phone)
            ->where('job_post_id', $this->jobPostId)
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($existing !== null) {
            $this->finish($existing->id);

            return;
        }

        $countryId = $this->form->country_id === 'other' ? null : (int) $this->form->country_id;

        $application = JobApplication::create([
            'job_post_id' => $this->jobPostId,
            'country_id' => $countryId,
            'full_name' => trim($this->form->full_name),
            'phone' => $phone,
            'age' => (int) $this->form->age,
            'district' => trim($this->form->district),
            // only the status is stored, never a passport number
            'passport_status' => PassportStatus::from($this->form->passport_status),
            'education' => $this->form->education !== '' ? $this->form->education : null,
            'experience' => $this->form->experience !== '' ? trim($this->form->experience) : null,
            'trade' => trim($this->form->trade),
            'message' => $this->form->message !== '' ? trim($this->form->message) : null,
            'status' => ApplicationStatus::New,
        ]);

        RateLimiter::hit($key, 3600);

        $this->finish($application->id);
    }

    private function finish(?int $id): void
    {
        $this->reference = $id !== null ? 'PK-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT) : null;
        $this->sent = true;
        $this->form->reset();
        $this->jobPostId = null;
    }

    public function render(): View
    {
        return view('livewire.pages.forms.apply', [
            'countries' => Country::active()->ordered()->get(['id', 'name']),
            'education' => ApplyForm::EDUCATION,
            'passportOptions' => [
                PassportStatus::Has->value => 'Yes',
                PassportStatus::None->value => 'No',
                PassportStatus::Processing->value => 'Applied (in process)',
            ],
        ]);
    }
}
