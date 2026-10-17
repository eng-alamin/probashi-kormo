<?php

namespace Database\Seeders;

use App\Models\TrainingCourse;
use Illuminate\Database\Seeder;

/**
 * Content copied from the original static site.
 * Uses firstOrCreate, so running it again never overwrites what an admin edited.
 */
class TrainingCourseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            TrainingCourse::firstOrCreate(['slug' => $row['slug']], $row);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        return [
            [
                'title' => 'Electrician',
                'slug' => 'electrician',
                'icon' => '⚡',
                'summary' => 'House and industrial wiring, switchboards, safety.',
                'description' => 'Learn to install, test and repair electrical systems safely. The course combines basic theory with plenty of hands-on practice.',
                'learn_points' => implode("\n", [
                    'Basic electrical theory and safety',
                    'House and building wiring',
                    'Switchboards, circuit breakers and fittings',
                    'Reading simple wiring diagrams',
                    'Fault finding and repair',
                ]),
                'career_points' => implode("\n", [
                    'Building and construction sites',
                    'Factories and workshops',
                    'Maintenance teams in hotels and offices',
                ]),
                'eligibility' => implode("\n", [
                    'Willingness to learn and work hard',
                ]),
                'sort_order' => 1,
            ],
            [
                'title' => 'Plumber',
                'slug' => 'plumber',
                'icon' => '🔧',
                'summary' => 'Pipe fitting, sanitary work, repair and maintenance.',
                'description' => 'Learn pipe work, sanitary fitting and repair for homes and buildings. The course combines basic theory with plenty of hands-on practice.',
                'learn_points' => implode("\n", [
                    'Pipes, fittings and tools',
                    'Water supply and drainage systems',
                    'Sanitary fixture installation',
                    'Leak detection and repair',
                    'Workplace safety',
                ]),
                'career_points' => implode("\n", [
                    'Construction projects',
                    'Hotels and buildings maintenance',
                    'Plumbing contractors',
                ]),
                'eligibility' => implode("\n", [
                    'Willingness to learn and work hard',
                ]),
                'sort_order' => 2,
            ],
            [
                'title' => 'Welder',
                'slug' => 'welder',
                'icon' => '🔥',
                'summary' => 'Arc and gas welding, metal cutting, fabrication.',
                'description' => 'Learn safe welding and metal fabrication skills in demand worldwide. The course combines basic theory with plenty of hands-on practice.',
                'learn_points' => implode("\n", [
                    'Welding safety and equipment',
                    'Arc welding basics',
                    'Gas cutting and welding',
                    'Metal joints and fabrication',
                    'Reading simple drawings',
                ]),
                'career_points' => implode("\n", [
                    'Fabrication workshops',
                    'Construction and steel work',
                    'Marine and industrial sites',
                ]),
                'eligibility' => implode("\n", [
                    'Willingness to learn and work hard',
                ]),
                'sort_order' => 3,
            ],
            [
                'title' => 'Mason',
                'slug' => 'mason',
                'icon' => '🧱',
                'summary' => 'Brick work, plastering, tiling, finishing.',
                'description' => 'Learn brick work, plastering, tiling and finishing for construction jobs. The course combines basic theory with plenty of hands-on practice.',
                'learn_points' => implode("\n", [
                    'Brick and block laying',
                    'Cement mixing and plastering',
                    'Tiling and finishing',
                    'Measuring and levelling',
                    'Site safety',
                ]),
                'career_points' => implode("\n", [
                    'Building construction',
                    'Finishing contractors',
                    'Housing projects',
                ]),
                'eligibility' => implode("\n", [
                    'Willingness to learn and work hard',
                ]),
                'sort_order' => 4,
            ],
            [
                'title' => 'Driver',
                'slug' => 'driver',
                'icon' => '🚗',
                'summary' => 'Safe driving skills, road rules, vehicle care.',
                'description' => 'Learn safe driving, road rules and basic vehicle care. The course combines basic theory with plenty of hands-on practice.',
                'learn_points' => implode("\n", [
                    'Vehicle controls and basic maintenance',
                    'Road rules and safe driving',
                    'Defensive driving habits',
                    'Basic troubleshooting',
                    'Preparing for the driving test abroad',
                ]),
                'career_points' => implode("\n", [
                    'Company and family driving',
                    'Delivery and transport',
                    'Light vehicle driving',
                ]),
                'eligibility' => implode("\n", [
                    'Willingness to learn and work hard',
                ]),
                'sort_order' => 5,
            ],
            [
                'title' => 'Cook / Kitchen Helper',
                'slug' => 'cook',
                'icon' => '🍳',
                'summary' => 'Food preparation, kitchen hygiene, basic cooking.',
                'description' => 'Learn kitchen basics, hygiene and food preparation for restaurant jobs. The course combines basic theory with plenty of hands-on practice.',
                'learn_points' => implode("\n", [
                    'Kitchen hygiene and food safety',
                    'Cutting and food preparation',
                    'Basic cooking methods',
                    'Working in a team in a busy kitchen',
                    'Basic kitchen vocabulary',
                ]),
                'career_points' => implode("\n", [
                    'Restaurants and hotels',
                    'Catering companies',
                    'Staff canteens',
                ]),
                'eligibility' => implode("\n", [
                    'Willingness to learn and work hard',
                ]),
                'sort_order' => 6,
            ],
            [
                'title' => 'Housekeeping',
                'slug' => 'housekeeping',
                'icon' => '🧹',
                'summary' => 'Cleaning, room service, laundry, hospitality basics.',
                'description' => 'Learn cleaning, room service and hospitality basics. The course combines basic theory with plenty of hands-on practice.',
                'learn_points' => implode("\n", [
                    'Cleaning methods and chemical safety',
                    'Room and bed preparation',
                    'Laundry and linen handling',
                    'Customer service basics',
                    'Workplace etiquette',
                ]),
                'career_points' => implode("\n", [
                    'Hotels and resorts',
                    'Office and building cleaning',
                    'Hospitals and facilities',
                ]),
                'eligibility' => implode("\n", [
                    'Willingness to learn and work hard',
                ]),
                'sort_order' => 7,
            ],
            [
                'title' => 'Tailoring & Garments',
                'slug' => 'tailoring',
                'icon' => '🧵',
                'summary' => 'Sewing machine operation, cutting, finishing.',
                'description' => 'Learn sewing machine operation and garment production skills. The course combines basic theory with plenty of hands-on practice.',
                'learn_points' => implode("\n", [
                    'Sewing machine operation',
                    'Cutting and stitching',
                    'Quality checking',
                    'Finishing and packing',
                    'Working to production targets',
                ]),
                'career_points' => implode("\n", [
                    'Garment factories',
                    'Tailoring shops',
                    'Production lines',
                ]),
                'eligibility' => implode("\n", [
                    'Willingness to learn and work hard',
                ]),
                'sort_order' => 8,
            ],
        ];
    }
}
