# Step 06 — Apply ও Contact form (database-এ save)

> আগে দুটো form শুধু WhatsApp খুলত, কিছু save হতো না। এখন সরাসরি database-এ যায়। Admin panel থেকে দেখা Step 07-এ।

## কী বানালাম

| Route | Name | কাজ |
|---|---|---|
| `/apply` | `apply` | চাকরির আবেদন → `job_applications` |
| `/apply?job={slug}` | `apply` | Job detail থেকে এলে চাকরি আর দেশ আগে থেকে বসানো, আবেদন ওই চাকরির সাথে যুক্ত |
| `/contact` | `contact` | বার্তা → `inquiries` |

- Livewire form object: `ApplyForm`, `ContactForm` (validation এখানে)
- Reusable Blade input: `x-site.field`, `select`, `textarea`, `honeypot`
- সফল হলে ফর্মের জায়গায় ধন্যবাদ-বার্তা। Apply-তে রেফারেন্স নম্বর (`PK-000123`) আর WhatsApp বোতাম।
- Contact page-এর ঠিকানা, ফোন, ইমেইল `config/site.php` থেকে। অফিস সময় আর Google Map `.env` থেকে, দেওয়া না থাকলে ওই অংশ দেখায় না ("Add your office hours here" আর দেখাবে না)।

## Validation ও সিদ্ধান্ত (ভুল মনে হলে বলো)
- **Passport নম্বর নেওয়া হয় না**, শুধু Yes / No / Applied (04-এর সিদ্ধান্ত অনুযায়ী)। ফর্মে লেখা আছে নম্বর না দিতে।
- **বয়স ১৮–৬৫।** বদলাতে চাইলে `ApplyForm::MIN_AGE / MAX_AGE`।
- **Phone:** ৭ থেকে ১৯ অক্ষরের সংখ্যা, শুরুতে `+` চলবে। স্পেস/হাইফেন সরিয়ে save হয় (`01712 345-678` → `01712345678`)। শুধু বাংলাদেশি মোবাইল নয়, কারণ বিদেশ থেকেও কেউ আবেদন করতে পারে।
- **Contact-এ message আবশ্যক** (static form-এ ঐচ্ছিক ছিল, কিন্তু `inquiries.message` column খালি রাখা যায় না)।
- Country dropdown এখন database-এর active দেশ থেকে, শেষে "Other / Not sure" (save হয় ফাঁকা দেশ হিসেবে)।
- Education: Below SSC, SSC, HSC, Diploma, Graduate।

## Spam ও নিরাপত্তা
- **Honeypot:** লুকানো একটা ঘর। বট ভরলে সফল দেখায় কিন্তু কিছু save হয় না।
- **Rate limit:** এক IP থেকে ঘণ্টায় সর্বোচ্চ ৫টা আবেদন, ৫টা বার্তা।
- **ডুপ্লিকেট:** একই phone + একই চাকরিতে ২৪ ঘণ্টায় দ্বিতীয়বার save হয় না (দুবার চাপ দিলে একটাই থাকে), ব্যবহারকারী সফল বার্তাই দেখে।
- `?job=` থেকে আসা চাকরির id `#[Locked]`, ব্রাউজার থেকে বদলানো যায় না। Slug খুঁজে না পেলে বা চাকরি বন্ধ হলে চুপচাপ উপেক্ষা।
- Map শুধু `https://www.google.com/maps/embed` দিয়ে শুরু হওয়া লিংক নেয়।
- সব লেখা Blade-এ escape হয়।

## আপনাকে করতে হবে (`.env`)
```
SITE_PHONE=...
SITE_EMAIL=...
SITE_ADDRESS=...
SITE_WHATSAPP=8801XXXXXXXXX   # country code সহ, + ছাড়া
SITE_HOURS="Sat–Thu, 9am–6pm"
SITE_MAP_EMBED="https://www.google.com/maps/embed?pb=..."   # Google Maps > Share > Embed a map > শুধু src এর লিংক
```
এগুলো না দিলে এখনও placeholder (`+880 1XXX-XXXXXX`, `Your office address`) জনসমক্ষে দেখাবে। Live করার আগে অবশ্যই বসাও।

## নতুন ফাইল
- `app/Livewire/Forms/{ApplyForm,ContactForm}.php`
- `app/Livewire/Pages/{Apply,Contact}.php`
- `resources/views/livewire/pages/forms/{apply,contact}.blade.php`
- `resources/views/components/site/{field,select,textarea,honeypot}.blade.php`
- `tests/Feature/Public/FormsTest.php`

## বদলানো ফাইল (replace)
`routes/web.php`, `config/site.php`, `resources/views/livewire/pages/jobs/show.blade.php` (Apply লিংক এখন slug দিয়ে), `public/site/css/extra.css` (তিনটা ছোট class যোগ), `docs/PROGRESS.md`

## করণীয় ধাপ
```bash
git status
# zip extract (Replace → Yes)
php artisan optimize:clear
php artisan test
./vendor/bin/pint
```
Browser-এ: `/contact` আর `/apply` ভরে পাঠাও, তারপর phpMyAdmin-এ `job_applications` ও `inquiries` দেখো। একটা চাকরি বানিয়ে `/jobs/{slug}` → "Apply for This Job" চেপে দেখো ফর্মে চাকরি বসেছে কিনা। পরীক্ষার সারিগুলো শেষে মুছে দাও।

```bash
git add .
git commit -m "feat: apply and contact forms saved to database"
```

## জানা সীমাবদ্ধতা
- নতুন আবেদন/বার্তা এলে **email বা notification যায় না** (mail setup নেই)। এখন শুধু database-এ থাকে। Admin panel-এ দেখা Step 07, dashboard-এর সংখ্যা Step 08।
- `public/site/js/main.js`-এর পুরনো WhatsApp কোড এখন নিষ্ক্রিয় (form id মেলে না), মুছলে ক্ষতি নেই।
- CAPTCHA নেই। Spam বাড়লে Cloudflare Turnstile যোগ করা যাবে।
- আবেদনকারীর ব্যক্তিগত তথ্য (নাম, ফোন, বয়স, ঠিকানা) জমছে। Production-এ HTTPS, database backup আর Privacy Policy-র সাথে মিল নিশ্চিত করো।

## সমস্যা হলে
| সমস্যা | সমাধান |
|---|---|
| Submit চাপলে কিছু হয় না | Browser console দেখো, `@livewireScripts` layout-এ আছে কিনা |
| `Too many applications` পরীক্ষার সময় | `php artisan cache:clear` |
| ফর্মে দেশ তালিকা ফাঁকা | `php artisan db:seed --class=CountrySeeder` |
