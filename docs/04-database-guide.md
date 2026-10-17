# Step 04 — Database Design + Migrations + Models

> এই step-এ শুধু database-এর কাঠামো (table, model) বানানো হলো। কোনো page বা form নেই। সেগুলো Step 05–07-এ আসবে।

## কী বানালাম

৯টা নতুন table, ৯টা model, ৩টা enum, আর একটা test file। পুরনো কোনো ফাইল বদলায়নি (শুধু `docs/PROGRESS.md` আপডেট হয়েছে)।

| Table | Model | কী রাখে |
|---|---|---|
| `countries` | `Country` | যেসব দেশে কর্মী পাঠানো হয় |
| `job_posts` | `JobPost` | চাকরির বিজ্ঞপ্তি (প্রতিটা একটা দেশের) |
| `job_applications` | `JobApplication` | Apply form-এ আসা আবেদন |
| `inquiries` | `Inquiry` | Contact form-এ আসা বার্তা |
| `services` | `Service` | Agency-র সেবা |
| `training_courses` | `TrainingCourse` | Training course |
| `gallery_items` | `GalleryItem` | Gallery-র ছবি |
| `news_posts` | `NewsPost` | খবর |
| `success_stories` | `SuccessStory` | সফল কর্মীদের গল্প |

**নাম নিয়ে সতর্কতা:** চাকরির table-এর নাম `jobs` না রেখে `job_posts` রেখেছি, কারণ Laravel-এর queue-র জন্য `jobs` নামে একটা table আগে থেকেই আছে। একই কারণে আবেদনের model-এর নাম `JobApplication`।

## সম্পর্ক (কে কার সাথে যুক্ত)

```
Country ──< JobPost ──< JobApplication >── Country
   │                          │
   ├──< Inquiry               └── handled_by → User
   └──< SuccessStory
User ──< NewsPost (author)
```

- দেশ মুছলে: ওই দেশে চাকরি থাকলে **মোছা যাবে না** (ভুলে চাকরি হারানো ঠেকাতে)। আবেদন, বার্তা ও success story থাকলে সেগুলো থেকে যাবে, শুধু দেশের সংযোগ ফাঁকা (`NULL`) হবে।
- চাকরি মুছলে: আবেদন থেকে যাবে, `job_post_id` ফাঁকা হবে। আবেদনকারীর তথ্য হারাবে না।
- কর্মী (User) মুছলে: আবেদন ও খবর থেকে যাবে, `handled_by` / `author_id` ফাঁকা হবে।

## সিদ্ধান্ত আর assumption (ভুল হলে বলো)

1. **Passport:** Apply form-এ passport-এর ঘরে কী যায় আমি দেখিনি (static site zip আমার কাছে নেই)। তাই `has / processing / none` এই তিন অবস্থা রেখেছি। **Passport নম্বর save করছি না**, কারণ এটা স্পর্শকাতর তথ্য আর আবেদনের জন্য দরকার নেই।
2. **Slug:** `countries`, `job_posts`, `services`, `training_courses`, `news_posts`-এ `slug` unique। URL-এ `/jobs/welder-saudi` ধরনের নাম আসবে। Slug তৈরি হবে Step 07-এর admin form-এ।
3. **ছবি:** শুধু ফাইলের path (`image_path`) save হয়। আসল upload Step 07-এ।
4. **Gallery:** আপাতত category ছাড়া সরল তালিকা। দরকার হলে পরে যোগ হবে।
5. **Salary:** `salary_min`, `salary_max`, `salary_currency` (যেমন `SAR`) আলাদা, কারণ বিদেশের বেতন টাকায় থাকে না।
6. **Seed data:** দেশ, চাকরি, course ইত্যাদির আসল লেখা তোমার static site-এর ৪০ page-এ আছে। আমি বানিয়ে লিখিনি। Step 05-এ page বানানোর সময় ওই content আনব।

## করণীয় ধাপ

### ধাপ ১: Git পরিষ্কার আছে কিনা দেখো
```bash
git status
```
Step 03 commit করা না থাকলে আগে commit করো।

### ধাপ ২: Zip extract করো
Project-এর **root**-এ (যেখানে `artisan` আছে) extract করো। শুধু `docs/PROGRESS.md` replace হবে, **Yes** দাও। বাকি সব নতুন ফাইল।

### ধাপ ৩: Table বানাও
```bash
php artisan optimize:clear
php artisan migrate
```
সফল হলে ৯টা `DONE` লাইন দেখাবে। phpMyAdmin-এ `probashi_kormo` database-এ নতুন table গুলো দেখো।

### ধাপ ৪: Test চালাও
```bash
php artisan test
```
আগের test-এর সাথে নতুন `ContentSchemaTest`-এর ১০টা test-ও সবুজ (pass) হওয়ার কথা।

### ধাপ ৫: Code style ঠিক করে commit করো
```bash
./vendor/bin/pint
git add .
git commit -m "feat: add content database schema, models and enums"
```
(PowerShell-এ pint: `vendor\bin\pint`)

## সমস্যা হলে

| সমস্যা | সমাধান |
|---|---|
| `Base table or view already exists` | ওই table আগে হাতে বানানো আছে। নাম আমাকে পাঠাও |
| `Can't create table ... errno: 150` | `php artisan migrate:status` দিয়ে দেখো আগের migration গুলো `Ran` কিনা |
| Test-এ `foreign key constraint` error | `config/database.php`-এ sqlite অংশে `foreign_key_constraints` মান `true` কিনা দেখো |
| নতুন class পাওয়া যাচ্ছে না | `composer dump-autoload` তারপর `php artisan optimize:clear` |

## পরের step (Step 05): Public pages
Home ছাড়া বাকি public page (About, Services, Jobs, Countries, Training, Gallery, News, Success Stories, Contact, Apply, Terms, Privacy), যেগুলো database থেকে data নেবে। Step 04 যাচাই করে জানাও।
