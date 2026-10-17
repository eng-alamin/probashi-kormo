<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\SuccessStory;
use Illuminate\Database\Seeder;

/**
 * SAMPLE stories so the page is not empty. The names and words below are placeholders, not real
 * workers. Replace them with real, consented stories in the admin before the site goes live.
 * Uses firstOrCreate, so running it again never overwrites what an admin edited.
 */
class SuccessStorySeeder extends Seeder
{
    public function run(): void
    {
        $countries = Country::pluck('id', 'slug');

        foreach ($this->rows() as $i => $row) {
            $country = $countries[$row['country']] ?? null;
            unset($row['country']);

            SuccessStory::firstOrCreate(
                ['name' => $row['name'], 'trade' => $row['trade']],
                $row + ['country_id' => $country, 'is_published' => true, 'sort_order' => $i],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        return [
            [
                'country' => 'saudi-arabia', 'name' => 'Rafiq', 'trade' => 'Electrician',
                'story' => 'I finished the electrician course, passed the trade test and was selected within a few weeks. Everything in the contract was explained to me before I signed.',
            ],
            [
                'country' => 'qatar', 'name' => 'Jahid', 'trade' => 'Welder',
                'story' => 'The practical training gave me confidence on the first day at work. The team stayed in touch with my family while the visa was processed.',
            ],
            [
                'country' => 'uae', 'name' => 'Sumon', 'trade' => 'Plumber',
                'story' => 'I had experience but no certificate. The training and skill test helped me get a better job than I expected.',
            ],
            [
                'country' => 'malaysia', 'name' => 'Nasima', 'trade' => 'Cook',
                'story' => 'As a first-time traveller I was nervous. The orientation class answered every question I had about life and work abroad.',
            ],
            [
                'country' => 'oman', 'name' => 'Kamal', 'trade' => 'Mason',
                'story' => 'No hidden charges and clear answers at every step. I would recommend them to anyone from my village.',
            ],
        ];
    }
}
