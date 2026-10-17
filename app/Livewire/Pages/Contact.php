<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\InquiryStatus;
use App\Livewire\Forms\ContactForm;
use App\Models\Country;
use App\Models\Inquiry;
use App\Support\Site;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Contact Us')]
class Contact extends Component
{
    private const MAX_PER_HOUR = 5;

    public ContactForm $form;

    public bool $sent = false;

    public function submit(): void
    {
        // Honeypot: pretend it worked, store nothing.
        if ($this->form->website !== '') {
            $this->finish();

            return;
        }

        $this->form->validate();

        $key = 'contact:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_PER_HOUR)) {
            throw ValidationException::withMessages([
                'form.message' => 'Too many messages from this connection. Please try again later or call us.',
            ]);
        }

        $phone = $this->form->normalizedPhone();
        $message = trim($this->form->message);

        // Double click / resubmit of the same message is not stored twice.
        $duplicate = Inquiry::query()
            ->where('phone', $phone)
            ->where('message', $message)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if (! $duplicate) {
            Inquiry::create([
                'country_id' => $this->form->country_id === 'other' ? null : (int) $this->form->country_id,
                'full_name' => trim($this->form->full_name),
                'phone' => $phone,
                'trade' => $this->form->trade !== '' ? trim($this->form->trade) : null,
                'message' => $message,
                'status' => InquiryStatus::New,
            ]);

            RateLimiter::hit($key, 3600);
        }

        $this->finish();
    }

    private function finish(): void
    {
        $this->sent = true;
        $this->form->reset();
    }

    public function render(): View
    {
        return view('livewire.pages.forms.contact', [
            'countries' => Country::active()->ordered()->get(['id', 'name']),
            // only Google Maps embed links are accepted (checked inside Site::mapEmbed)
            'mapUrl' => Site::mapEmbed(),
        ]);
    }
}
