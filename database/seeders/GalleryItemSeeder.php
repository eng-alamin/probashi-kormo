<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Starter gallery with simple generated placeholder pictures (SVG, saved on the "public" disk).
 * They are NOT real photos: replace them with real ones from the admin before the site goes live.
 * Needs `php artisan storage:link`. Uses firstOrCreate, so running it again never overwrites edits.
 */
class GalleryItemSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $i => $row) {
            $path = 'gallery/sample-'.($i + 1).'.svg';

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $this->svg($row['icon'], $row['title'], $row['from'], $row['to']));
            }

            GalleryItem::firstOrCreate(['image_path' => $path], [
                'title' => $row['title'],
                'caption' => $row['caption'],
                'category' => $row['category'],
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function rows(): array
    {
        return [
            ['category' => 'office', 'icon' => '🏢', 'title' => 'Our Office', 'caption' => 'Where you meet our team and get your questions answered.', 'from' => '#006a4e', 'to' => '#004d38'],
            ['category' => 'office', 'icon' => '🤝', 'title' => 'Counselling', 'caption' => 'One-to-one guidance before you apply.', 'from' => '#0a8f6a', 'to' => '#006a4e'],
            ['category' => 'training', 'icon' => '🔧', 'title' => 'Hands-on Training', 'caption' => 'Practice with real tools in every course.', 'from' => '#e0202f', 'to' => '#991b1b'],
            ['category' => 'training', 'icon' => '⚡', 'title' => 'Electrical Workshop', 'caption' => 'Wiring practice for future electricians.', 'from' => '#004d38', 'to' => '#0a8f6a'],
            ['category' => 'departure', 'icon' => '✈️', 'title' => 'Ready to Fly', 'caption' => 'Workers leaving for their jobs abroad.', 'from' => '#0a8f6a', 'to' => '#004d38'],
            ['category' => 'departure', 'icon' => '🎓', 'title' => 'Pre-departure Briefing', 'caption' => 'Last orientation before the flight.', 'from' => '#b91c2b', 'to' => '#e0202f'],
        ];
    }

    private function svg(string $icon, string $title, string $from, string $to): string
    {
        $title = htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" role="img" aria-label="{$title}">
  <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$from}"/><stop offset="1" stop-color="{$to}"/></linearGradient></defs>
  <rect width="800" height="600" fill="url(#g)"/>
  <circle cx="680" cy="110" r="150" fill="#fff" fill-opacity=".10"/>
  <circle cx="90" cy="540" r="190" fill="#fff" fill-opacity=".08"/>
  <text x="400" y="310" font-size="150" text-anchor="middle">{$icon}</text>
  <text x="400" y="430" font-family="Arial, sans-serif" font-size="44" font-weight="700" fill="#fff" text-anchor="middle">{$title}</text>
</svg>
SVG;
    }
}
