<?php

namespace Database\Seeders;

use App\Models\NewsPost;
use Illuminate\Database\Seeder;

/**
 * Starter notices and guides. General advice only, with no dates, fees or claims about the agency.
 * Edit or replace them in the admin before the site goes live.
 * Uses firstOrCreate, so running it again never overwrites what an admin edited.
 */
class NewsPostSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $i => $row) {
            NewsPost::firstOrCreate(['slug' => $row['slug']], $row + [
                'is_published' => true,
                'published_at' => now()->subDays(3 + $i * 6),
            ]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        return [
            [
                'title' => 'How to Avoid Overseas Job Fraud',
                'slug' => 'avoid-overseas-job-fraud',
                'excerpt' => 'Simple checks that protect you and your family before you pay or sign anything.',
                'body' => "Every year people lose their savings to fake job offers abroad. A few simple checks can keep you safe.\n\nAlways confirm that the agency is licensed and that you receive a written contract before you pay anything. Never pay a person who will not give you a receipt, and never hand over your passport without a clear written reason.\n\nBe careful with offers that sound too good: very high salaries for no experience, visa on a tourist permit, or pressure to decide today. A genuine employer will explain the job, salary, working hours and contract length clearly.\n\nIf you are unsure, visit our office and ask. We are happy to explain every step.",
            ],
            [
                'title' => 'Documents You Need Before You Apply',
                'slug' => 'documents-needed-before-you-apply',
                'excerpt' => 'Get these ready first and your application will move faster.',
                'body' => "Having your papers ready saves weeks. Most jobs abroad ask for the following.\n\nA valid passport with enough validity left, your national ID card, recent passport-size photographs, and your education or training certificates. If you have worked before, bring experience letters.\n\nIf you do not have a passport yet, you can still apply. Tell us and we will guide you on how to get one.\n\nKeep copies of every document and never give originals to anyone you do not trust.",
            ],
            [
                'title' => 'Why Skill Training Helps You Get a Better Job Abroad',
                'slug' => 'why-skill-training-matters',
                'excerpt' => 'Trained and tested workers are chosen first by employers.',
                'body' => "Employers abroad prefer workers who can start the job on day one. A trained worker is safer on site, makes fewer mistakes and is trusted with better work.\n\nSkill training also helps you pass the trade test that many employers ask for before selecting a candidate. It gives you confidence with tools, safety rules and the vocabulary used on the job.\n\nOur courses are hands-on. You practise with real tools and learn what the work abroad actually looks like.\n\nSee the Training page for the trades we teach and talk to us about the right course for you.",
            ],
            [
                'title' => 'Medical and Visa Process: What to Expect',
                'slug' => 'medical-and-visa-process',
                'excerpt' => 'A plain-language look at the steps between selection and your flight.',
                'body' => "After you are selected, there are a few steps before you can fly.\n\nFirst comes a medical check at an approved centre. Then your documents are prepared and submitted for the work visa. Some countries also ask for a skill test certificate or police clearance.\n\nTimelines depend on the country and the employer, so we explain the expected steps at the start and keep you updated at each stage.\n\nPlease respond quickly when we ask for documents. Delays on your side are the most common reason for a late flight.",
            ],
            [
                'title' => 'Know Your Contract Before You Sign',
                'slug' => 'know-your-contract-before-you-sign',
                'excerpt' => 'Salary, hours, duration and benefits should all be written down.',
                'body' => "Your employment contract is your protection. Read it fully before you sign, and ask us to explain anything that is unclear.\n\nCheck the job title, monthly salary, working hours, overtime rules, contract length, accommodation and food arrangements, and the return ticket terms.\n\nThe job and salary in your contract must match what you were told. If something is different, do not sign. Speak to us first.\n\nKeep a copy of the signed contract with you when you travel.",
            ],
            [
                'title' => 'Apply Online in a Few Minutes',
                'slug' => 'apply-online',
                'excerpt' => 'You can now send your application from your phone.',
                'body' => "You no longer need to visit the office to start an application. Open the Apply page, fill in your name, phone number, trade and the country you prefer, and send it.\n\nOur team will call you to discuss the next steps. Please keep your phone on and your documents ready.\n\nYou will never be asked to pay to submit the online form.",
            ],
        ];
    }
}
