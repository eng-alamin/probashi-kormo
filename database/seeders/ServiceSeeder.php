<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Content copied from the original static site.
 * Uses firstOrCreate, so running it again never overwrites what an admin edited.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            Service::firstOrCreate(['slug' => $row['slug']], $row);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        return [
            [
                'title' => 'Skill Training',
                'slug' => 'skill-training',
                'icon' => '🎓',
                'summary' => 'Hands-on, job-oriented training in different trades so you are ready for the work abroad.',
                'what_we_do' => implode("\n", [
                    'Hands-on courses in popular overseas trades',
                    'Experienced instructors and real tools',
                    'Basic language and workplace orientation',
                    'Skill test and evaluation',
                ]),
                'what_to_bring' => implode("\n", [
                    'Passport-size photos',
                    'National ID card',
                    'Previous experience details, if any',
                ]),
                'sort_order' => 1,
            ],
            [
                'title' => 'Manpower Recruitment',
                'slug' => 'manpower-recruitment',
                'icon' => '🧳',
                'summary' => 'We connect trained workers with verified overseas employers and manage the selection process.',
                'what_we_do' => implode("\n", [
                    'Collect and review employer demand',
                    'Shortlist suitable candidates',
                    'Arrange interviews and trade tests',
                    'Explain contract terms clearly',
                ]),
                'what_to_bring' => implode("\n", [
                    'Passport and National ID',
                    'Educational / skill certificates',
                    'CV or experience details',
                ]),
                'sort_order' => 2,
            ],
            [
                'title' => 'Visa Processing',
                'slug' => 'visa-processing',
                'icon' => '📄',
                'summary' => 'Complete help with documents, applications and follow-up until your visa is ready.',
                'what_we_do' => implode("\n", [
                    'Check and prepare required documents',
                    'Submit the visa application',
                    'Follow up on progress',
                    'Keep you informed at each step',
                ]),
                'what_to_bring' => implode("\n", [
                    'Valid passport',
                    'Passport-size photos',
                    'Required certificates',
                    'Medical report when required',
                ]),
                'sort_order' => 3,
            ],
            [
                'title' => 'Medical & Clearance',
                'slug' => 'medical-clearance',
                'icon' => '🩺',
                'summary' => 'Guidance for the medical check-up, BMET registration and other required government clearance.',
                'what_we_do' => implode("\n", [
                    'Guide you to approved medical centers',
                    'Help with BMET registration and clearance',
                    'Explain each requirement before you start',
                    'Keep your documents organized',
                ]),
                'what_to_bring' => implode("\n", [
                    'Passport',
                    'National ID card',
                    'Medical reports',
                    'Visa and job documents',
                ]),
                'sort_order' => 4,
            ],
            [
                'title' => 'Pre-Departure Orientation',
                'slug' => 'pre-departure-orientation',
                'icon' => '🧭',
                'summary' => 'Clear briefing on your job, contract, culture, rules and rights in the destination country.',
                'what_we_do' => implode("\n", [
                    'Explain your job and contract',
                    'Brief on local culture and laws',
                    'Tips for living and working abroad',
                    'Who to contact if you face a problem',
                ]),
                'what_to_bring' => implode("\n", [
                    'Your contract and job documents',
                    'Questions you want to ask',
                ]),
                'sort_order' => 5,
            ],
            [
                'title' => 'Air Ticket & Travel',
                'slug' => 'air-ticket-travel',
                'icon' => '✈️',
                'summary' => 'Ticketing and travel arrangement so your journey is smooth from airport to workplace.',
                'what_we_do' => implode("\n", [
                    'Book air tickets for your travel date',
                    'Share flight and airport guidance',
                    'Explain baggage rules',
                    'Support on the day of departure',
                ]),
                'what_to_bring' => implode("\n", [
                    'Passport and visa',
                    'Travel date confirmation',
                    'Contract and job documents',
                ]),
                'sort_order' => 6,
            ],
        ];
    }
}
