<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\PassportStatus;
use App\Models\Country;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ApplyForm extends Form
{
    public const EDUCATION = ['Below SSC', 'SSC', 'HSC', 'Diploma', 'Graduate'];

    public const MIN_AGE = 18;

    public const MAX_AGE = 65;

    public string $full_name = '';

    public string $phone = '';

    public string $age = '';

    public string $district = '';

    public string $passport_status = '';

    public string $education = '';

    /** A country id, or "other" for "Other / Not sure". */
    public string $country_id = '';

    public string $trade = '';

    public string $experience = '';

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
            'age' => ['required', 'integer', 'between:'.self::MIN_AGE.','.self::MAX_AGE],
            'district' => ['required', 'string', 'max:100'],
            'passport_status' => ['required', Rule::in(PassportStatus::values())],
            'education' => ['nullable', Rule::in(self::EDUCATION)],
            'country_id' => ['required', Rule::in([...$countryIds, 'other'])],
            'trade' => ['required', 'string', 'max:150'],
            'experience' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number, for example 01XXXXXXXXX.',
            'age.between' => 'Age must be between '.self::MIN_AGE.' and '.self::MAX_AGE.'.',
            'country_id.in' => 'Please choose a country from the list.',
            'passport_status.in' => 'Please choose an option.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'full_name' => 'full name',
            'passport_status' => 'passport answer',
            'country_id' => 'country',
            'trade' => 'job / trade',
        ];
    }

    public function normalizedPhone(): string
    {
        return (string) preg_replace('/[\s\-]+/', '', $this->phone);
    }
}
