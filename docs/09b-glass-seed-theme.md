# Step 09b — Admin light theme, Glassmorphism site, Content seeders

## ১. Admin: default light
- `layouts/admin.blade.php`, `layouts/auth.blade.php`, `resources/js/admin.js`: ডিফল্ট `light`
- localStorage key বদলে `pk-admin-theme-v2` — আগে ব্রাউজারে `dark` সেভ থাকলে সেটা আর আটকে থাকে না। Topbar-এর বোতামে dark বেছে নিলে সেটা মনে থাকবে
- **`npm run build` লাগবে** (`admin.js` Vite দিয়ে যায়)

## ২. Site: Glassmorphism
- নতুন `public/site/css/glass.css` (`site.blade.php`-এ `extra.css`-এর পরে লোড হয়); Tailwind build লাগে না
- পেছনে নরম সবুজ/লাল blob গ্রেডিয়েন্ট (`body::before`), তার ওপর frosted glass
- Glass হয়: sticky header ও mobile menu, সব card (`.card`, `bg-white` + `rounded-*`), FAQ, stats, gallery tile, pill বোতাম, form field, footer (dark glass)
- Hero / banner / CTA-র গ্রেডিয়েন্ট আগের মতোই
- নতুন view-তে ব্যবহারের জন্য helper: `.glass`, `.glass-strong`, `.glass-dark`
- `backdrop-filter` না থাকলে আর `prefers-reduced-transparency` চালু থাকলে প্রায় solid surface
- রং/blur বদলাতে `glass.css`-এর ওপরের `:root` variable দেখো

## ৩. Content seeders
নতুন seeder (`DatabaseSeeder` থেকে চলে, সবই `firstOrCreate` — admin-এর edit মোছে না, দুইবার চালালে ডুপ্লিকেট হয় না):
| Seeder | কী বসায় |
|---|---|
| `JobPostSeeder` | ৭টা খোলা চাকরি (প্রতি দেশে একটা), requirements/benefits/বেতন সহ |
| `NewsPostSeeder` | ৬টা published পোস্ট (fraud সতর্কতা, কাগজপত্র, visa process ইত্যাদি) |
| `SuccessStorySeeder` | ৫টা গল্প |
| `GalleryItemSeeder` | ৬টা item, ছবি হিসেবে generated SVG placeholder (`storage/app/public/gallery/sample-N.svg`) |

**live করার আগে অবশ্যই:** এগুলো **নমুনা লেখা**। Job-এর বেতন/সুবিধা, Success Story-র নাম ও কথা, Gallery-র ছবি আসল নয়। আসল তথ্য দিয়ে admin থেকে বদলাও বা মুছে দাও। ভুয়া গল্প/বেতন আসল ওয়েবসাইটে রাখা ঠিক না।

চালাও:
```
php artisan storage:link
php artisan db:seed
```
(শুধু নতুনগুলো: `php artisan db:seed --class=JobPostSeeder`, `NewsPostSeeder`, `SuccessStorySeeder`, `GalleryItemSeeder`। Jobs/Stories আগে `CountrySeeder` লাগে।)

## ৪. যা এখনও seeder/database-এ নেই
About, Terms, Privacy Policy-র লেখা, Home-এর stats (500+ ইত্যাদি) ও hero লেখা এখনও Blade view-তে। এগুলো admin থেকে বদলাতে চাইলে আলাদা `pages`/`site_settings` table লাগবে।

## Test
`tests/Feature/Database/SiteContentSeedTest.php`: seeder-এর সংখ্যা, দেশের সাথে যোগ, placeholder ফাইল, আবার চালালে edit থাকে, public পাতায় দেখায়, `glass.css` লোড, admin light default।
