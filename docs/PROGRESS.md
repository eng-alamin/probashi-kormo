# Probashi Kormo — Progress Log

প্রতিটা step শেষে এই ফাইলে কী কী হলো লেখা হবে।

| Step | বিষয় | Status |
|------|-------|--------|
| 00 | Project analysis (দুই zip দেখা, plan) | Done |
| 01 | Laravel 12 + Livewire 4 + MySQL setup | Done |
| 02 | Frontend foundation (Vite, Bootstrap 5, public + admin layout) | Done (তোমার মেশিনে যাচাই হওয়ার অপেক্ষায়) |
| 03 | Admin login + Roles | Done (তোমার মেশিনে যাচাই হওয়ার অপেক্ষায়) |
| 04 | Database design + migrations | Done (তোমার মেশিনে যাচাই হওয়ার অপেক্ষায়) |
| 05A | Static site মিলিয়ে database ঠিক + seed (দেশ, সেবা, course) | Done (তোমার মেশিনে যাচাই হওয়ার অপেক্ষায়) |
| 05B-1 | Public pages: Services, Countries, Training + Home (database থেকে) | Done (তোমার মেশিনে যাচাই হওয়ার অপেক্ষায়) |
| 05B-2 | Public pages: Jobs, Gallery, News, Success Stories, About, Terms, Privacy | Done (তোমার মেশিনে যাচাই হওয়ার অপেক্ষায়) |
| 06 | Livewire forms (Apply, Contact) database-এ save | Done (তোমার মেশিনে যাচাই হওয়ার অপেক্ষায়) |
| 07A | Admin: Applications, Inquiries + permission কাঠামো | Done (তোমার মেশিনে test চালানো বাকি) |
| 07B | Admin CRUD: Jobs, Countries | Done (তোমার মেশিনে test চালানো বাকি) |
| 07C | Admin CRUD: Training, Services | Done (তোমার মেশিনে test চালানো বাকি) |
| 07D | Admin CRUD: Gallery, News, Success Stories (ছবি upload সহ) | Done (তোমার মেশিনে test চালানো বাকি) |
| 07E | Admin: Staff Users, Roles & Permissions | Done (তোমার মেশিনে test চালানো বাকি) |
| 08 | Dashboard (real data) + Activity log | Done (তোমার মেশিনে test চালানো বাকি) |
| 09 | SEO (dynamic sitemap) + Testing | Done (তোমার মেশিনে test চালানো বাকি) |
| 09b | Admin ডিফল্ট light + Glassmorphism site + content seeders | Done (তোমার মেশিনে test চালানো বাকি) |
| 09c | Admin Settings (site info + home page text) | Done (তোমার মেশিনে test চালানো বাকি) |
| 10 | Production deploy | Pending (file তৈরি, doc বাকি) |

## Step 01 — যা যা করা হয়েছে
- Laravel 12 project, Livewire 4, MySQL database `probashi_kormo`
- `pint.json` (PSR-12), `.vscode/extensions.json`, Git init

## Step 02 — যা যা করা হয়েছে
- Public site = আগের Tailwind (compiled ফাইল), Admin = Bootstrap 5 (Vite)
- Class-based Livewire component, `Route::livewire()`
- Public layout + Home, Admin layout + Dashboard shell
- `config/site.php`, `config/admin_menu.php`, `App\Support\SafeRoute`

## Step 03 — যা যা করা হয়েছে
- `spatie/laravel-permission:^6.0` (v7 PHP 8.3 চায়, আমাদের PHP 8.2 তাই v6)
- তিনটা role: `Super Admin`, `Admin`, `Staff` (`App\Enums\RoleName`)
- `users` table-এ `is_active` column (account বন্ধ করার জন্য)
- Livewire login page `/admin/login` (`LoginForm` দিয়ে validation, ৫ বার ভুল হলে সাময়িক block)
- Logout (`POST /admin/logout`)
- `EnsureAdminAccess` middleware: login + active + role না থাকলে ঢুকতে দেয় না; Livewire-এর প্রতিটা update request-এও আবার চেক হয়
- Super Admin সব permission পায় (`Gate::before`)
- Seeder: `RoleSeeder`, `AdminUserSeeder` (email/password `.env` থেকে, কোডে hardcode নেই)
- Test: `tests/Feature/Admin/AdminAccessTest.php`, `tests/Feature/Auth/LoginTest.php`

## Step 04 — যা যা করা হয়েছে
- ৯টা table: `countries`, `job_posts`, `job_applications`, `inquiries`, `services`, `training_courses`, `gallery_items`, `news_posts`, `success_stories`
- চাকরির table-এর নাম `job_posts` (Laravel-এর queue-র `jobs` table-এর সাথে নাম না মেলার জন্য)
- ৯টা model (scope, relation, cast সহ), ৩টা enum (`ApplicationStatus`, `InquiryStatus`, `PassportStatus`)
- Foreign key নিয়ম: চাকরি থাকলে দেশ মোছা যায় না; বাকি জায়গায় মুছলে সংযোগ ফাঁকা (`NULL`) হয়, তথ্য হারায় না
- Passport নম্বর save হয় না, শুধু অবস্থা (`has / processing / none`)
- Test: `tests/Feature/Database/ContentSchemaTest.php`
- Seed data এখনও নেই। আসল content static site থেকে Step 05-এ আসবে

## Step 05A — যা যা করা হয়েছে
- Static site-এর ৪০ page পড়ে দেখা হয়েছে কোন তথ্যের জায়গা database-এ নেই
- নতুন migration: country (flag, job_sectors, contract_info, extra_info), job (category), application (trade), service (what_we_do, what_to_bring), course (icon, learn_points, career_points, eligibility, class_days, certificate_info), gallery (category)
- তালিকার ঘর = text, প্রতি লাইনে একটা আইটেম
- Seed: ৭ দেশ, ৬ সেবা, ৮ course (static site-এর HTML থেকে তোলা, `firstOrCreate` তাই admin-এর edit মোছে না)
- তখন seed করা হয়নি: Jobs, News, Success Stories, Gallery (এগুলো পরে Step 09b-তে নমুনা হিসেবে যোগ হয়েছে), আর "add here" লেখা লাইন
- Course-এর Duration/Fee/Class days/Certificate এখনও ফাঁকা, আসল তথ্য দরকার
- Test: `tests/Feature/Database/ContentSeedTest.php`

## Step 05B-1 — যা যা করা হয়েছে
- Routes: `/services`, `/countries`, `/training` আর প্রতিটার `/{slug}` detail
- Class-based Livewire component (`Index`, `Show`), শুধু active item, বন্ধ item 404
- Reusable Blade component: `x-site.banner`, `checklist`, `info-row`, `steps`, `faq`, `help-box`, `cta`, `chips`
- `App\Support\Lines`: লাইন-ভাগ করা text থেকে তালিকা
- Home-এ database-এর service, country, course
- ফাঁকা তথ্য "Contact us" দেখায়, per-page meta description
- Test: `tests/Feature/Public/PublicPagesTest.php`
- Tailwind নতুন করে build লাগেনি (আগের class-ই ব্যবহার)

## Step 05B-2 — যা যা করা হয়েছে
- Routes: `/jobs`, `/jobs/{slug}`, `/gallery`, `/success-stories`, `/news`, `/news/{slug}`, `/about`, `/terms`, `/privacy-policy`
- Job: খোলা চাকরি তালিকায়, deadline পার হলে detail-এ "closed" আর Apply বন্ধ, inactive হলে 404
- News: published + তারিখ পার হলে তবেই public, body escape করা
- About / Terms / Privacy static site-এর লেখা থেকে; হলুদ sample সতর্কবার্তা বাদ; License No এখন `SITE_LICENSE_NO` থেকে
- `public/site/css/extra.css` (দুটো ছোট utility)
- Test: `tests/Feature/Public/PublicPagesPartTwoTest.php`
- করণীয়: `SITE_LICENSE_NO` বসানো, Terms/Privacy আইনি পর্যালোচনা, `php artisan storage:link`

## Step 06 — যা যা করা হয়েছে
- `/apply` → `job_applications`, `/contact` → `inquiries` (আগে শুধু WhatsApp)
- `?job={slug}` দিয়ে চাকরি আর দেশ আগে থেকে বসানো, আবেদন চাকরির সাথে যুক্ত
- Livewire form object `ApplyForm`, `ContactForm`; reusable `x-site.field/select/textarea/honeypot`
- Validation: বয়স ১৮–৬৫, phone স্বাভাবিক করে save, passport নম্বর নেওয়া হয় না
- Spam: honeypot, IP-প্রতি ঘণ্টায় ৫টা, একই phone+job ২৪ ঘণ্টায় ডুপ্লিকেট নয়
- Contact page-এর অফিস সময় ও Google Map `.env` থেকে (`SITE_HOURS`, `SITE_MAP_EMBED`)
- Test: `tests/Feature/Public/FormsTest.php`
- করণীয়: `.env`-এ আসল phone/email/address/WhatsApp বসানো (এখনও placeholder)

## Step 06b — যা যা করা হয়েছে
- যোগাযোগ তথ্যের ভুয়া default সরানো; ফাঁকা বা placeholder হলে ওই অংশ লুকায় (`App\Support\Site`)
- WhatsApp বোতাম ঠিক নম্বর ছাড়া দেখায় না; phone/email ক্লিকযোগ্য
- `php artisan site:check` (live করার আগে চালাও)
- করণীয়: `docs/env-site-example.txt` দেখে `.env` পূরণ

## Step 07A — যা যা করা হয়েছে
- `config/permissions.php` + `PermissionSeeder`: প্রতি module-এ `view/create/update/delete`; Admin = Users/Roles ছাড়া সব, Staff = Applications ও Inquiries দেখা+বদলানো
- Applications: তালিকা (search, status, দেশ filter, pagination, delete), বিস্তারিত (status, internal note, `handled_by`)
- Inquiries: তালিকা, বিস্তারিত (খুললে Read, status বদল, delete)
- Route `can:` middleware + প্রতিটা Livewire action-এ `authorize()`
- Menu item permission অনুযায়ী লুকায় (`can` key)
- Test: `tests/Feature/Admin/ApplicationsInquiriesTest.php`
- করণীয়: `php artisan db:seed --class=PermissionSeeder`

## Step 07B — যা যা করা হয়েছে
- Jobs: তালিকা (search, দেশ/অবস্থা filter, Visible switch, Featured, delete) + নতুন/edit form
- Countries: তালিকা (Visible switch, delete শুধু চাকরি না থাকলে) + নতুন/edit form
- `App\Support\Slug` (unique slug, Bangla-only নামে fallback), `x-admin.field/textarea/select/check` component
- Test: `tests/Feature/Admin/JobsCountriesAdminTest.php`
- করণীয়: নেই (নতুন migration/seeder নেই)

## Step 07C — যা যা করা হয়েছে
- Training: তালিকা (search, Visible switch, delete) + নতুন/edit form (duration, fee, class days, certificate, তিনটা তালিকা)
- Services: তালিকা + form (what we do, what to bring)
- Test: `tests/Feature/Admin/TrainingsServicesAdminTest.php`
- করণীয়: নেই (নতুন migration/seeder নেই)। Seed করা course-এর Duration/Fee এখন admin থেকে বসানো যাবে

## Step 07D — যা যা করা হয়েছে
- `App\Support\ImageStore`: সব upload এক জায়গায় (JPG/PNG/WebP, সর্বোচ্চ 4 MB, SVG নয়, `public` disk, random নাম, মুছলে ফাইলও মোছে, শুধু নিজের ফোল্ডারে)
- Gallery: তালিকা (search, category filter, Visible switch, delete) + একসাথে ২০টা পর্যন্ত ছবি upload (preview, ✕ দিয়ে বাদ) + একটা ছবি edit/replace
- News: তালিকা (search, Live/Scheduled/Draft filter, Published switch, delete) + form (cover ছবি, remove, draft/schedule/publish, author বসে, slug)
- Success Stories: তালিকা (search, Visible switch, delete) + form (country, গোল photo, remove)
- নতুন reusable `x-admin.image` component, admin.css-এ upload/thumbnail অংশ
- Public news তালিকায় cover ছবি দেখায়
- Test: `tests/Feature/Admin/GalleryNewsStoriesAdminTest.php` (gd extension লাগে)
- করণীয়: `php artisan storage:link`, `php.ini`-তে `upload_max_filesize=5M` / `post_max_size=64M`, `npm run build`

## Step 07E — যা যা করা হয়েছে
- Staff Users: তালিকা (search, role filter, Active switch, delete) + form (নাম, email, password ≥১০ অক্ষর + অক্ষর/সংখ্যা + confirm, একটা role, Active)
- Roles & Permissions: তিনটা role স্থির; Admin ও Staff-এর permission মেট্রিক্সে টিক দিয়ে বদলানো; View নিজে থেকে চালু হয় (Add/Edit/Delete-এর সাথে); Reset to default (Save-এর আগে পর্যন্ত শুধু form-এ)
- `App\Support\StaffAccess`: Super Admin-এর account শুধু Super Admin ছুঁতে পারে; শেষ সক্রিয় Super Admin মোছা/বন্ধ/নামানো যায় না; নিজের role/Active/delete লক
- Users ও Roles section-এর টিক শুধু Super Admin বদলাতে পারে (Admin নিজেকে ক্ষমতা দিতে পারে না)
- `config/permissions.php`-এ `labels`, `action_labels` যোগ
- Test: `tests/Feature/Admin/UsersRolesAdminTest.php`
- করণীয়: `npm run build`, `php artisan optimize:clear`

## Step 08 — যা যা করা হয়েছে
- Dashboard: চারটা card (Applications, Open Jobs, Training Courses, New Messages) আসল সংখ্যায়; শেষের ৫ application ও message; status অনুযায়ী ভাগ; ৭ দিনের বার; শেষের activity
- Dashboard permission মানে: যে section খোলার অনুমতি নেই তার তথ্য বানানোই হয় না (Staff শুধু Applications ও Messages দেখে)
- `activity_logs` table (`user_id` + `actor_name` কপি, `event`, `subject_type/id`, `description`, `ip_address`, `created_at`)
- `config/activity.php`: কোন model দেখা হবে + `ACTIVITY_LOG_DAYS` (ডিফল্ট ১৮০)
- `ActivityObserver`: লগইন থাকা কেউ create/update/delete করলে line লেখে; শুধু ঘরের নাম, মান নয় (password/ফোন/নোট log-এ যায় না)
- Status বদল আলাদা বর্ণনায়; নতুন inquiry খুললে নিজে থেকে Read হওয়া log হয় না
- Role বদল (Users form), permission বদল (Roles form), Staff-এর Sign in / Sign out log হয়
- Applications, Inquiries, Jobs, Services, Training-এর delete `whereKey()->delete()` থেকে `find()?->delete()`-এ, যাতে log ধরা পড়ে
- Activity Log পাতা `/admin/logs` (search, event ও staff filter); permission `logs.view`, ডিফল্টে শুধু Super Admin
- `php artisan activity:prune [--days=N]` + প্রতিদিনের schedule (server-এ cron লাগবে)
- Test: `tests/Feature/Admin/DashboardTest.php`, `tests/Feature/Admin/ActivityLogTest.php`
- করণীয়: `php artisan migrate`, `php artisan db:seed --class=PermissionSeeder`, `php artisan optimize:clear`; production-এ cron (`docs/08-dashboard-activity-log.md`)

## Step 09 — যা যা করা হয়েছে
- `/sitemap.xml` (dynamic: static পাতা + active দেশ/সেবা/course + খোলা job + published news) আর `/robots.txt` (production-এ `/admin` বন্ধ + Sitemap লাইন; অন্য env-এ সব বন্ধ)
- Public layout-এ canonical, Open Graph, Twitter card; news cover ছবি share image
- JSON-LD: Home-এ `EmploymentAgency`, খোলা job-এ `JobPosting` (`App\Support\Seo`)
- Test: `tests/Feature/SeoTest.php`
- করণীয়: `public/robots.txt` মোছা, `.env`-এ `APP_ENV=production` + আসল `APP_URL`, Search Console-এ sitemap জমা (`docs/09-seo-testing.md`)

## Step 09b — যা যা করা হয়েছে
- Admin ডিফল্ট theme `light` (storage key `pk-admin-theme-v2`); `npm run build` লাগবে
- Site-এ `public/site/css/glass.css`: header, card, FAQ, form, footer glass; পেছনে gradient blob; `.glass` helper
- Seeder: `JobPostSeeder` (৭), `NewsPostSeeder` (৬), `SuccessStorySeeder` (৫), `GalleryItemSeeder` (৬, SVG placeholder)
- Test: `tests/Feature/Database/SiteContentSeedTest.php`
- করণীয়: `php artisan storage:link`, `php artisan db:seed`, `npm run build`; **নমুনা লেখা live-এর আগে আসল তথ্যে বদলানো** (`docs/09b-glass-seed-theme.md`)

## Step 09c — যা যা করা হয়েছে
- `site_settings` table (key/value) + `App\Support\Settings` (একবার read, table না থাকলেও site চলে)
- `Site` helper এখন আগে database, না থাকলে `.env`/`config/site.php`: phone, email, address, WhatsApp, hours, map, license; নতুন `Site::home()`
- Admin → Settings (`/admin/settings`): যোগাযোগের তথ্য + Home page-এর heading/লেখা/৪টা সংখ্যা। আর "Soon" নেই
- Permission `settings.view/update` (Admin ডিফল্টে পায়, Staff পায় না); save-এর লগ হয় (মান ছাড়া)
- ফাঁকা বক্স = `.env`-এর মান; Heading-এর লাল অংশ ইচ্ছা করে ফাঁকা রাখা যায়
- Test: `tests/Feature/Admin/SettingsTest.php`
- করণীয়: `php artisan migrate`, `php artisan db:seed --class=PermissionSeeder`, `php artisan optimize:clear`

## জানা সীমাবদ্ধতা
- নতুন আবেদন/বার্তায় email/notification যায় না। Admin-এ দেখা Step 07-এ।
- Password reset ("Forgot password") এখনও নেই। দরকার হলে পরে যোগ হবে (mail setup লাগবে)।
- নতুন role বানানো/মোছা যায় না (ইচ্ছা করে), শুধু Admin ও Staff-এর permission বদলানো যায় (Step 07E)।
- Admin theme-এর রং এখনও আগের template-এর নীল।
- About/Terms/Privacy-র লেখা এখনও Blade view-তে। Home-এর heading/stats এখন Settings থেকে বদলায় (Step 09c)।
- My Profile (নিজের নাম/email/password বদল) এখনও নেই।
- আপলোড করা ছবি resize/compress হয় না (Step 07D)। Country ও Training course-এর ছবির ঘর এখনও form-এ নেই।
- ভুল password দিয়ে লগইন চেষ্টা Activity Log-এ লেখা হয় না (ইচ্ছা করে, Step 08)।
- Activity Log-এর লেখা ইংরেজিতে; Remember-me দিয়ে আবার ঢুকলেও "Signed in" লেখা হয়।
