<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\JobPost;
use Illuminate\Database\Seeder;

/**
 * Starter job openings so the Jobs pages are not empty.
 * The wording, salary ranges and benefits are SAMPLE text: check and edit them in the admin
 * (or delete them) before the site goes live.
 * Uses firstOrCreate, so running it again never overwrites what an admin edited.
 */
class JobPostSeeder extends Seeder
{
    public function run(): void
    {
        $countries = Country::pluck('id', 'slug');

        foreach ($this->rows() as $row) {
            $countryId = $countries[$row['country']] ?? null;
            unset($row['country']);

            JobPost::firstOrCreate(['slug' => $row['slug']], $row + ['country_id' => $countryId]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        $apply = "Valid passport (or passport in process)\nNational ID card\nPassport-size photos\nEducation and training certificates\nExperience letters, if any";

        return [
            [
                'country' => 'saudi-arabia',
                'title' => 'Electrician',
                'slug' => 'electrician-saudi-arabia',
                'category' => 'Construction',
                'summary' => 'Skilled electricians needed for building and maintenance projects.',
                'description' => "Install, test and repair electrical wiring, panels and lighting in residential and commercial buildings. Work follows the site supervisor's drawings and safety rules.",
                'requirements' => "Age 22–40\nAt least 2 years of wiring experience or a trade certificate\nBasic understanding of safety rules\n".$apply,
                'benefits' => "Monthly salary as per contract\nContract terms explained before you sign\nOvertime as per company policy",
                'salary_min' => 1200, 'salary_max' => 1800, 'salary_currency' => 'SAR',
                'vacancies' => 20, 'is_featured' => true, 'is_active' => true,
            ],
            [
                'country' => 'uae',
                'title' => 'Plumber',
                'slug' => 'plumber-uae',
                'category' => 'Construction',
                'summary' => 'Plumbers for pipe fitting, repair and maintenance work.',
                'description' => 'Fit and repair water supply and drainage pipes, fixtures and pumps. Read basic plans and keep the work area safe and clean.',
                'requirements' => "Age 21–40\nPipe fitting experience or trade certificate\nAble to work on site in hot weather\n".$apply,
                'benefits' => "Monthly salary as per contract\nContract terms explained before you sign",
                'salary_min' => 1300, 'salary_max' => 1900, 'salary_currency' => 'AED',
                'vacancies' => 10, 'is_featured' => true, 'is_active' => true,
            ],
            [
                'country' => 'qatar',
                'title' => 'Welder (Arc / MIG)',
                'slug' => 'welder-qatar',
                'category' => 'Manufacturing',
                'summary' => 'Experienced welders for fabrication and site work.',
                'description' => 'Cut, join and finish metal parts by arc or MIG welding. Follow drawings and quality checks, and wear the required safety gear at all times.',
                'requirements' => "Age 22–42\nWelding experience or trade certificate\nWilling to take a skill test\n".$apply,
                'benefits' => "Monthly salary as per contract\nOvertime as per company policy",
                'salary_min' => 1400, 'salary_max' => 2000, 'salary_currency' => 'QAR',
                'vacancies' => 15, 'is_featured' => true, 'is_active' => true,
            ],
            [
                'country' => 'oman',
                'title' => 'Mason',
                'slug' => 'mason-oman',
                'category' => 'Construction',
                'summary' => 'Masons for brick, block and plaster work.',
                'description' => 'Lay bricks and blocks, plaster walls and finish surfaces to the required level on building sites.',
                'requirements' => "Age 21–42\nAt least 1 year of masonry experience\nPhysically fit for site work\n".$apply,
                'benefits' => "Monthly salary as per contract\nContract terms explained before you sign",
                'salary_min' => 130, 'salary_max' => 180, 'salary_currency' => 'OMR',
                'vacancies' => 25, 'is_featured' => false, 'is_active' => true,
            ],
            [
                'country' => 'kuwait',
                'title' => 'Light Vehicle Driver',
                'slug' => 'driver-kuwait',
                'category' => 'Transport',
                'summary' => 'Drivers for company and family transport.',
                'description' => 'Drive company or family vehicles safely, keep the vehicle clean and in good order, and follow the assigned routes and timings.',
                'requirements' => "Age 25–45\nValid driving licence with at least 2 years of driving\nClean driving record\n".$apply,
                'benefits' => "Monthly salary as per contract\nContract terms explained before you sign",
                'salary_min' => 110, 'salary_max' => 150, 'salary_currency' => 'KWD',
                'vacancies' => 8, 'is_featured' => false, 'is_active' => true,
            ],
            [
                'country' => 'malaysia',
                'title' => 'Cook / Kitchen Helper',
                'slug' => 'cook-malaysia',
                'category' => 'Hospitality',
                'summary' => 'Cooks and kitchen helpers for restaurants and canteens.',
                'description' => 'Prepare, cook and serve food to the restaurant menu, keep the kitchen clean, and follow food hygiene rules.',
                'requirements' => "Age 20–40\nKitchen experience or cooking training\nUnderstands basic food hygiene\n".$apply,
                'benefits' => "Monthly salary as per contract\nContract terms explained before you sign",
                'salary_min' => 1800, 'salary_max' => 2400, 'salary_currency' => 'MYR',
                'vacancies' => 12, 'is_featured' => false, 'is_active' => true,
            ],
            [
                'country' => 'singapore',
                'title' => 'Housekeeping Staff',
                'slug' => 'housekeeping-singapore',
                'category' => 'Hospitality',
                'summary' => 'Housekeeping staff for hotels and serviced buildings.',
                'description' => 'Clean and prepare rooms and common areas, change linen, and report any damage or missing items to the supervisor.',
                'requirements' => "Age 21–40\nHousekeeping experience or training\nBasic English or Malay helps\n".$apply,
                'benefits' => "Monthly salary as per contract\nContract terms explained before you sign",
                'salary_min' => 700, 'salary_max' => 1000, 'salary_currency' => 'SGD',
                'vacancies' => 10, 'is_featured' => false, 'is_active' => true,
            ],
        ];
    }
}
