# Step 07B — Admin: Jobs ও Countries

> আগে Step 07A যাচাই করে নাও (`php artisan test` সবুজ, Applications/Inquiries পাতা খোলে)।

## কী বানালাম

| ফাইল | কাজ |
|---|---|
| `app/Livewire/Admin/Jobs/Index.php` + view | চাকরির তালিকা: search, দেশ ও অবস্থা filter (Open / Closed / Hidden), আবেদনের সংখ্যা, Visible switch, Featured তারা, delete |
| `app/Livewire/Admin/Jobs/Form.php` + view | নতুন চাকরি ও edit (একই form) |
| `app/Livewire/Admin/Countries/Index.php`, `Form.php` + view | দেশের তালিকা ও form (flag, order, job sectors, contract info) |
| `app/Support/Slug.php` | নামের থেকে unique slug বানায়; Bangla-only নাম হলে `job-xxxxxx` ধরনের fallback |
| `resources/views/components/admin/*` | form-এর ছোট অংশ: `x-admin.field`, `textarea`, `select`, `check` (পরের module-এও ব্যবহার হবে) |
| `routes/web.php` | `admin.jobs.index/create/edit`, `admin.countries.index/create/edit` |
| `resources/css/admin.css` | form grid ও hint style |
| `tests/Feature/Admin/JobsCountriesAdminTest.php` | স্বয়ংক্রিয় test |

## নিয়মগুলো

- **Slug:** ফাঁকা রাখলে নাম থেকে তৈরি হয়, মিললে `-2`, `-3` যোগ হয়। Edit করার সময় আগের slug থাকে। নিজে বদলালে public লিংক বদলে যায়, তাই form-এ সতর্কবার্তা আছে।
- **Salary:** সব ফাঁকা রাখলে public পাতায় "As per contract" দেখায়। Maximum, minimum-এর কম হতে পারে না। Currency ৩ অক্ষরের (SAR, AED, MYR), ছোট হাতের লিখলে বড় হাতের হয়ে যায়।
- **Job মুছলে:** আবেদন থেকে যায়, শুধু চাকরির সাথে সংযোগ ফাঁকা হয় (Step 04-এর নিয়ম)। সাময়িকভাবে সরাতে Visible switch ব্যবহার করো।
- **Country মুছলে:** চাকরি থাকলে মোছা যায় না, পরিষ্কার বার্তা আসে। Visible switch দিয়ে লুকানো যায়।
- **Permission:** Staff Jobs/Countries দেখতেই পারে না (menu-তেও নেই)। Admin ও Super Admin পারে। প্রতিটা action-এ আলাদা `authorize()` আছে।

## করণীয় ধাপ

### ধাপ ১: Git পরিষ্কার আছে কিনা দেখো
```bash
git status
```

### ধাপ ২: Zip extract করো
Project root-এ, "Replace" জিজ্ঞাসা করলে **Yes**। বদলাবে: `routes/web.php`, `resources/css/admin.css`, `docs/PROGRESS.md`।

### ধাপ ৩: Test ও cache
```bash
php artisan optimize:clear
php artisan test
```
নতুন seeder নেই, নতুন migration নেই।

### ধাপ ৪: Browser-এ যাচাই (`php artisan serve` + `npm run dev`)
- [ ] Sidebar-এ Jobs ও Countries আর "Soon" নয়
- [ ] Countries-এ সাত দেশ দেখায়, একটার Visible switch বন্ধ করলে public `/countries`-এ আর দেখায় না
- [ ] নতুন দেশ বানাও, flag emoji দাও, public পাতায় দেখো
- [ ] নতুন চাকরি বানাও (salary, deadline সহ), `/jobs`-এ দেখায়, detail পাতায় salary ঠিক আছে
- [ ] Deadline গতকালের দিয়ে সংরক্ষণ করো: Admin-এ "Closed", public detail-এ closed বার্তা
- [ ] চাকরি Hidden করলে public লিংক 404
- [ ] আবেদন আছে এমন চাকরি মুছলে Applications পাতায় আবেদন থাকে, Job ঘর "—" হয়
- [ ] চাকরি আছে এমন দেশ মুছতে গেলে লাল বার্তা আসে
- [ ] Staff account দিয়ে ঢুকলে Jobs/Countries menu নেই, সরাসরি URL-এ 403
- [ ] মোবাইল width-এ form এক কলামে, তালিকা card আকারে

## সীমাবদ্ধতা
- এই sandbox-এ Laravel বসানো নেই, তাই test চালানো যায়নি। শুধু PHP syntax যাচাই হয়েছে।
- Country-র `image_path` এখনও form-এ নেই, public পাতাও ওটা ব্যবহার করে না। ছবি upload Step 07D (Gallery/News) এর সাথে আসবে।
- Job-এর "Featured" শুধু admin তালিকার তারা। Public home এখনও ওটা ব্যবহার করে না।

## পরের step (07C): Training ও Services CRUD
