# Step 05A — Static site মিলিয়ে Database ঠিক করা + Seed data

> Step 05 (Public pages) বড়, তাই ছোট ভাগে করছি। আগে ৪০টা page পড়ে দেখলাম database-এ কী কী কম পড়ছে। এই ভাগে শুধু database ঠিক করা আর আসল লেখা (content) বসানো। Page বানানো **05B**-তে।

## static site পড়ে কী পেলাম

| বিষয় | static site-এ আছে | Step 04-এ ছিল না, তাই যোগ করলাম |
|---|---|---|
| Country | পতাকা (🇶🇦), "Common job sectors" তালিকা, Contract info, অতিরিক্ত তথ্যের box | `flag`, `job_sectors`, `contract_info`, `extra_info` |
| Job | Category (Construction, Fabrication, Transport, Hospitality) | `category` |
| Apply form | "Job / Trade" ঘর (লেখা, আবশ্যক) | `job_applications.trade` |
| Service | "What We Do" ও "What You Need to Bring" তালিকা | `what_we_do`, `what_to_bring` |
| Training | আইকন, What You Will Learn, Where You Can Work, Who Can Join, Class days, Certificate | `icon`, `learn_points`, `career_points`, `eligibility`, `class_days`, `certificate_info` |
| Gallery | Office / Training / Departure ফিল্টার | `category` (`GalleryCategory` enum) |

তালিকার ঘরগুলো (যেমন `job_sectors`) সাধারণ text, **প্রতি লাইনে একটা আইটেম**। Admin form-এ একটা textarea-তে লিখলেই হবে। Page-এ দেখানোর সময় লাইন ধরে ভেঙে ✔ দিয়ে সাজাব (05B-তে)।

নিশ্চিত হয়েছি: Apply form-এ passport-এর বিকল্প **Yes / No / Applied**, এটা আমার `PassportStatus` (`has / none / processing`) এর সাথে মিলে যায়। বদলাতে হয়নি।

## কী কী আছে এই zip-এ

| ফাইল | কাজ |
|---|---|
| `database/migrations/2026_10_19_000001_add_public_page_fields_to_content_tables.php` | **নতুন** migration। পুরনো table ভাঙে না, শুধু column যোগ করে |
| `app/Models/{Country,JobPost,JobApplication,Service,TrainingCourse,GalleryItem}.php` | **(replace)** নতুন column `fillable`-এ যোগ |
| `app/Enums/GalleryCategory.php` | **নতুন** |
| `database/seeders/CountrySeeder.php` | ৭টা দেশ |
| `database/seeders/ServiceSeeder.php` | ৬টা সেবা |
| `database/seeders/TrainingCourseSeeder.php` | ৮টা course |
| `database/seeders/DatabaseSeeder.php` | **(replace)** নতুন seeder গুলো যোগ |
| `tests/Feature/Database/ContentSeedTest.php` | Seed-এর test |
| `docs/PROGRESS.md` | **(replace)** |

Seeder-এর লেখা স্ক্রিপ্ট দিয়ে সরাসরি আপনার static site-এর HTML থেকে তোলা, হাতে টাইপ করিনি। `firstOrCreate` ব্যবহার করেছি, তাই seeder আবার চালালে admin-এর বদলানো লেখা মুছে যাবে না।

## কী seed করিনি (ইচ্ছা করেই)

- **Jobs, News, Success Stories, Gallery:** static site-এ এগুলো পুরোটাই নমুনা (লেখা আছে "Sample listing", "Add date", "Worker Name")। নকল চাকরি বা নকল সফলতার গল্প একটা recruitment website-এ থাকা ঝুঁকির, মানুষ ওই চাকরির জন্য আবেদন করতে পারে। আসল data admin panel (Step 07) দিয়ে দেবেন।
- **"Add here" লেখা লাইনগুলো:** যেমন "Age and education requirement: add here", এগুলো বাদ দিয়ে seed হয়েছে। Course-এর Duration, Fee, Class days, Certificate এখন ফাঁকা। আসল তথ্য আপনাকে দিতে হবে।
- **FAQ আর "Documents Usually Needed":** সব দেশের পেজে একই লেখা, তাই database-এ না রেখে 05B-তে Blade-এ একবার লিখব।

## করণীয় ধাপ

### ধাপ ১: Step 04 আগে ঠিক আছে কিনা
```bash
php artisan migrate:status
```
Step 04-এর ৯টা migration `Ran` না থাকলে আগে `php artisan migrate` চালাও।

### ধাপ ২: Zip extract করো
Project-এর root-এ। "Replace" জিজ্ঞাসা করলে **Yes**।

### ধাপ ৩: Table বদলাও আর content বসাও
```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=CountrySeeder
php artisan db:seed --class=ServiceSeeder
php artisan db:seed --class=TrainingCourseSeeder
```
(`php artisan db:seed` একা চালালেও হয়, ওটা Role ও Admin seeder-ও চালাবে, নিরাপদ।)

phpMyAdmin-এ দেখো: `countries` ৭, `services` ৬, `training_courses` ৮ সারি।

### ধাপ ৪: Test
```bash
php artisan test
```

### ধাপ ৫: Style ঠিক করে commit
```bash
./vendor/bin/pint
git add .
git commit -m "feat: align content schema with static site and seed base content"
```

## সমস্যা হলে

| সমস্যা | সমাধান |
|---|---|
| `Duplicate column name` | Migration আংশিক চলেছে। কোন column বলছে, আমাকে পাঠাও |
| `Table 'countries' doesn't exist` | Step 04-এর migration চলেনি, ধাপ ১ দেখো |
| `Class "Database\Seeders\CountrySeeder" not found` | `composer dump-autoload` |
| Emoji `????` দেখাচ্ছে | Database collation `utf8mb4_unicode_ci` কিনা দেখো (docs/01 ধাপ ৪) |

## পরের ভাগ (05B): Page বানানো
Services, Countries, Training, Jobs-এর তালিকা + detail page, তারপর Gallery, News, Success Stories, About, Terms, Privacy, আর শেষে Apply ও Contact form (Step 06)। তার আগে Tailwind-এর CSS নতুন করে build করব, কারণ নতুন Blade-এ নতুন class লাগতে পারে। Step 05A যাচাই করে জানাও।
