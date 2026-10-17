<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

/**
 * Content copied from the original static site.
 * Uses firstOrCreate, so running it again never overwrites what an admin edited.
 */
class CountrySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            Country::firstOrCreate(['slug' => $row['slug']], $row);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        return [
            [
                'name' => 'Saudi Arabia',
                'slug' => 'saudi-arabia',
                'flag' => '🇸🇦',
                'summary' => 'Construction, hospitality, driving, maintenance, domestic and service work.',
                'description' => 'Saudi Arabia offers job opportunities for workers in several sectors. Available jobs, salary and working conditions depend on the employer and current demand, and are stated in your contract. Contact us for the latest openings.',
                'job_sectors' => implode("\n", [
                    'Construction',
                    'Hospitality and restaurants',
                    'Driving and transport',
                    'Maintenance and technical work',
                    'Cleaning and service jobs',
                ]),
                'sort_order' => 1,
            ],
            [
                'name' => 'United Arab Emirates',
                'slug' => 'uae',
                'flag' => '🇦🇪',
                'summary' => 'Construction, hotels and restaurants, retail, security, cleaning and technical work.',
                'description' => 'United Arab Emirates offers job opportunities for workers in several sectors. Available jobs, salary and working conditions depend on the employer and current demand, and are stated in your contract. Contact us for the latest openings.',
                'job_sectors' => implode("\n", [
                    'Construction',
                    'Hotels and restaurants',
                    'Retail and sales',
                    'Security',
                    'Cleaning and technical work',
                ]),
                'sort_order' => 2,
            ],
            [
                'name' => 'Qatar',
                'slug' => 'qatar',
                'flag' => '🇶🇦',
                'summary' => 'Construction, facility management, hospitality, driving and skilled trades.',
                'description' => 'Qatar offers job opportunities for workers in several sectors. Available jobs, salary and working conditions depend on the employer and current demand, and are stated in your contract. Contact us for the latest openings.',
                'job_sectors' => implode("\n", [
                    'Construction',
                    'Facility management',
                    'Hospitality',
                    'Driving',
                    'Skilled trades',
                ]),
                'sort_order' => 3,
            ],
            [
                'name' => 'Oman',
                'slug' => 'oman',
                'flag' => '🇴🇲',
                'summary' => 'Construction, hospitality, maintenance, agriculture and service sectors.',
                'description' => 'Oman offers job opportunities for workers in several sectors. Available jobs, salary and working conditions depend on the employer and current demand, and are stated in your contract. Contact us for the latest openings.',
                'job_sectors' => implode("\n", [
                    'Construction',
                    'Hospitality',
                    'Maintenance',
                    'Agriculture',
                    'Service sectors',
                ]),
                'sort_order' => 4,
            ],
            [
                'name' => 'Kuwait',
                'slug' => 'kuwait',
                'flag' => '🇰🇼',
                'summary' => 'Construction, cleaning, service, hospitality and technical jobs.',
                'description' => 'Kuwait offers job opportunities for workers in several sectors. Available jobs, salary and working conditions depend on the employer and current demand, and are stated in your contract. Contact us for the latest openings.',
                'job_sectors' => implode("\n", [
                    'Construction',
                    'Cleaning and service',
                    'Hospitality',
                    'Technical jobs',
                ]),
                'sort_order' => 5,
            ],
            [
                'name' => 'Malaysia',
                'slug' => 'malaysia',
                'flag' => '🇲🇾',
                'summary' => 'Manufacturing, plantation, construction, food service and factory work.',
                'description' => 'Malaysia offers job opportunities for workers in several sectors. Available jobs, salary and working conditions depend on the employer and current demand, and are stated in your contract. Contact us for the latest openings.',
                'job_sectors' => implode("\n", [
                    'Manufacturing',
                    'Plantation',
                    'Construction',
                    'Food service',
                    'Factory work',
                ]),
                'sort_order' => 6,
            ],
            [
                'name' => 'Singapore',
                'slug' => 'singapore',
                'flag' => '🇸🇬',
                'summary' => 'Construction, marine, manufacturing, hospitality and technical trades.',
                'description' => 'Singapore offers job opportunities for workers in several sectors. Available jobs, salary and working conditions depend on the employer and current demand, and are stated in your contract. Contact us for the latest openings.',
                'job_sectors' => implode("\n", [
                    'Construction',
                    'Marine',
                    'Manufacturing',
                    'Hospitality',
                    'Technical trades',
                ]),
                'sort_order' => 7,
            ],
        ];
    }
}
