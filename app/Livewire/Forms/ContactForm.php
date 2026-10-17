<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Country;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ContactForm extends Form
{
    public string $full_name = '';

    public string $phone = '';

    /** A country id, or "other" for "Other / Not sure". */
    public string $country_id = '';

    public string $trade = '';

    public string $message = '';

    /** Honeypot: real visitors never see or fill this. */
    public string $website = '';

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $countryIds = Country::active()->pluck('id')->map(fn ($id) => (string) $id)->all();

        return [
            'full_name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9][0-9\s\-]{6,18}$/'],
            'country_id' => ['required', Rule::in([...$countryIds, 'other'])],
            'trade' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number, for example 01XXXXXXXXX.',
            'country_id.in' => 'Please choose a country from the list.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'full_name' => 'full name',
            'country_id' => 'country',
        ];
    }

    public function normalizedPhone(): string
    {
        return (string) preg_replace('/[\s\-]+/', '', $this->phone);
    }
}
