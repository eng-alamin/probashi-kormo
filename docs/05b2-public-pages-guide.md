# Step 05B-2 — Public pages: Jobs, Gallery, News, Success Stories, About, Terms, Privacy

> 05B-1-এর পর বাকি public page। Apply আর Contact form Step 06-এ।

## কী বানালাম

| Route | Name | Page |
|---|---|---|
| `/jobs` | `jobs` | খোলা চাকরির তালিকা, দেশ ধরে filter (শুধু যে দেশে খোলা চাকরি আছে) |
| `/jobs/{slug}` | `jobs.show` | Job detail: requirements, benefits, বেতন, vacancies, last date |
| `/gallery` | `gallery` | ছবি, Office / Training / Departure filter |
| `/success-stories` | `success-stories` | প্রকাশিত গল্প |
| `/news` | `news` | সর্বশেষ ৩০টা notice |
| `/news/{slug}` | `news.show` | একটা notice পুরোটা (static site-এ ছিল না, `body` column আছে বলে যোগ করলাম) |
| `/about`, `/terms`, `/privacy-policy` | `about`, `terms`, `privacy-policy` | static site-এর লেখা হুবহু |

## নিয়ম (সিদ্ধান্ত)
- **Job:** তালিকায় শুধু `is_active` আর deadline পার হয়নি এমন চাকরি। Deadline পার হলে তালিকা থেকে বাদ, কিন্তু সরাসরি লিংকে গেলে "This opening is closed" দেখায় আর Apply বোতাম থাকে না। `is_active = false` হলে 404।
- **Apply বোতাম:** `apply` route এখনও নেই, তাই লিংক `#`। Step 06-এ route বানালেই নিজে থেকে `?job=...` সহ কাজ করবে।
- **বেতন:** min/max থাকলে `SAR 1,000 – 1,500 / month`, না থাকলে "As per contract"।
- **News:** `is_published` + `published_at` এখন বা আগের হলে তবেই দেখায়। ভবিষ্যতের তারিখ বা draft হলে 404। `body` এখন সাধারণ লেখা (escape করা), ফাঁকা লাইনে নতুন অনুচ্ছেদ। HTML দিলে চলবে না, নিরাপত্তার জন্য ইচ্ছা করে।
- **Gallery / Story ছবি:** `storage/` থেকে দেখায়। ছবি দেখাতে একবার `php artisan storage:link` চালাতে হবে (Step 07-এ upload আসবে)।
- **কিছু না থাকলে:** Jobs আর Success Stories-এ ভদ্র "এখন কিছু নেই" বার্তা। কারণ এই দুটো ইচ্ছা করে seed করা হয়নি (05A দেখো)।
- **Static site-এর হলুদ "sample / template" সতর্কবার্তা** public output-এ বাদ দিয়েছি।
- **About-এর License No:** `XXXX` সরিয়ে `.env`-এর `SITE_LICENSE_NO` থেকে আসে। ফাঁকা থাকলে লাইনটাই দেখায় না।

## আপনাকে করতে হবে
1. `.env`-এ দিন: `SITE_LICENSE_NO=...` (আসল recruiting license নম্বর)
2. **Terms আর Privacy Policy একজন আইন-উপদেষ্টাকে দিয়ে দেখিয়ে নিন।** static site-এ লেখা ছিল এটা "general template"। আমি বাক্য বদলাইনি, শুধু সতর্কবার্তাটা সরিয়েছি, তাই দায়িত্ব এখন আপনার।
3. `php artisan storage:link` (ছবি দেখাতে)

## নতুন ফাইল
- `app/Livewire/Pages/{Jobs,News}/{Index,Show}.php`
- `app/Livewire/Pages/{Gallery,SuccessStories,About,Terms,PrivacyPolicy}.php`
- `resources/views/livewire/pages/{jobs,news}/{index,show}.blade.php`
- `resources/views/livewire/pages/gallery/index.blade.php`, `stories/index.blade.php`
- `resources/views/livewire/pages/{about,terms,privacy-policy}.blade.php`
- `public/site/css/extra.css` (দুটো ছোট class: `col-span-full`, `whitespace-pre-line`, যেগুলো আগের compiled CSS-এ ছিল না)
- `tests/Feature/Public/PublicPagesPartTwoTest.php`

## বদলানো ফাইল (replace)
`routes/web.php`, `config/site.php`, `resources/views/layouts/site.blade.php` (extra.css link), `docs/PROGRESS.md`

## করণীয় ধাপ
```bash
git status
# zip extract (Replace → Yes)
php artisan optimize:clear
php artisan storage:link
php artisan route:list --name=jobs
php artisan test
./vendor/bin/pint
```
Browser-এ দেখো: `/about`, `/jobs`, `/gallery`, `/news`, `/success-stories`, `/terms`, `/privacy-policy`। এখন Nav আর Footer-এর প্রায় সব link আসল page-এ যাবে। বাকি থাকল `apply` ও `contact` (Step 06)।

নমুনা চাকরি দিয়ে চেষ্টা করতে চাইলে `php artisan tinker` থেকে একটা `JobPost::create([...])` দিতে পারো, শেষে মুছে দিও (নকল চাকরি public site-এ রেখে দিও না)।

```bash
git add .
git commit -m "feat: public pages for jobs, gallery, news, stories and static pages"
```

## জানা সীমাবদ্ধতা
- News তালিকায় pagination নেই (সর্বশেষ ৩০টা)। Admin module-এর সাথে যোগ হবে।
- `extra.css` সাময়িক। Laravel-এর ভেতরে Tailwind build বসালে (content path-এ `resources/views`) ওই ফাইল বাদ দেওয়া যাবে।
- SEO (dynamic sitemap, OG tag) Step 09-এ।

## সমস্যা হলে
| সমস্যা | সমাধান |
|---|---|
| ছবি ভাঙা | `php artisan storage:link` |
| `Route [jobs.show] not defined` | `php artisan optimize:clear` |
| Job detail 404 | `is_active` ঠিক আছে কিনা, slug মিলছে কিনা |
