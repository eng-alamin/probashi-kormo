# Step 03 — Admin Login + Roles

> আগে Step 02 তোমার মেশিনে ঠিকমতো চলছে কিনা দেখে নাও (`/` আর `/admin` খোলে কিনা)। এই step তার উপরেই দাঁড়িয়ে আছে। Step 02-তে কোনো error থাকলে আগে সেটা আমাকে পাঠাও।

## কী বানালাম

এখন থেকে `/admin` ঢুকতে হলে লগইন লাগবে। শুধু যাদের তিনটা role-এর একটা আছে (Super Admin, Admin, Staff) আর account চালু (active) আছে, তারাই ঢুকতে পারবে।

| ফাইল | কাজ |
|---|---|
| `app/Enums/RoleName.php` | তিনটা role-এর নাম এক জায়গায় |
| `app/Models/User.php` | **(replace)** role সুবিধা + `is_active` + `canAccessAdmin()` |
| `app/Providers/AppServiceProvider.php` | **(replace)** Super Admin সব অনুমতি পায় + Livewire-এ middleware চেক |
| `app/Http/Middleware/EnsureAdminAccess.php` | admin panel-এর পাহারাদার |
| `app/Http/Controllers/Auth/LogoutController.php` | Logout |
| `app/Livewire/Auth/Login.php` + view | Login page |
| `app/Livewire/Forms/LoginForm.php` | Login-এর validation + ভুল পাসওয়ার্ডের limit |
| `resources/views/layouts/auth.blade.php` | Login page-এর layout |
| `routes/web.php` | **(replace)** login, logout, protected admin route |
| `database/migrations/2026_10_17_000001_add_is_active_to_users_table.php` | `users`-এ `is_active` column |
| `config/admin.php` | প্রথম Super Admin-এর তথ্য (`.env` থেকে) |
| `database/seeders/*` | **DatabaseSeeder replace**, RoleSeeder ও AdminUserSeeder নতুন |
| `tests/Feature/...` | স্বয়ংক্রিয় test |

## নিরাপত্তার যে বিষয়গুলো খেয়াল রেখেছি
- ভুল email আর ভুল password-এ একই বার্তা দেখায় (কে account-ধারী বোঝা যায় না)।
- একই email + IP থেকে ৫ বার ভুল করলে কিছুক্ষণের জন্য block।
- Login-এর পর session নতুন করে তৈরি হয় (session fixation আটকাতে)।
- Admin password কোডে লেখা নেই, `.env` থেকে আসে, আর seeder আবার চালালে পুরনো password বদলায় না।
- Livewire-এ প্রতিটা বাটন ক্লিকও সার্ভারে request হয়। তাই `EnsureAdminAccess` প্রতিটা update request-এও আবার চেক হয়। কারও account বন্ধ করলে সে আগে থেকে খোলা page-এ কিছু করতে গেলেও আটকে যাবে।

## করণীয় ধাপ

### ধাপ ১: Git পরিষ্কার আছে কিনা দেখো
```bash
git status
```
Step 02-এর কাজ commit করা না থাকলে আগে commit করে নাও।

### ধাপ ২: Role package বসাও
```bash
composer require spatie/laravel-permission:^6.0
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

**কেন `:^6.0`?** Spatie-র নতুন version (7) PHP **8.3** চায়, কিন্তু তোমার XAMPP-তে PHP **8.2.12**। তাই version 6 নিতে হবে, না হলে composer error দেবে। (ভবিষ্যতে PHP 8.3-এ আপগ্রেড করলে তখন version 7-এ যাওয়া যাবে।)

দ্বিতীয় command-টা `config/permission.php` আর role-এর table-এর migration তৈরি করবে।

### ধাপ ৩: Zip extract করো
Project-এর root-এ extract করো। "Replace" জিজ্ঞাসা করলে **Yes**। এই চারটা পুরনো ফাইল বদলাবে: `routes/web.php`, `app/Models/User.php`, `app/Providers/AppServiceProvider.php`, `database/seeders/DatabaseSeeder.php`।

### ধাপ ৪: `.env`-এ প্রথম Super Admin-এর তথ্য দাও
`.env`-এর নিচে যোগ করো (নিজের আসল email ও **শক্ত password**, কমপক্ষে ১০ অক্ষর):

```env
ADMIN_NAME="Your Name"
ADMIN_EMAIL=you@example.com
ADMIN_PASSWORD=
```

`ADMIN_PASSWORD=` এর পর নিজের পছন্দের শক্ত password বসাও। এই password অন্য কাউকে দিও না, কোথাও commit করো না। (`.env` Git-এ যায় না।)

### ধাপ ৫: Cache পরিষ্কার, table বানাও, admin তৈরি করো
```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed
```

`db:seed`-এর শেষে `Super Admin ready: your-email` দেখাতে হবে। এর পর `.env` থেকে `ADMIN_PASSWORD` লাইনটা মুছে দিতে পারো (password এখন database-এ লুকানো অবস্থায় আছে)।

### ধাপ ৬: Browser-এ যাচাই করো
(`php artisan serve` আর `npm run dev` দুটোই চালু রাখো।)

- [ ] `http://127.0.0.1:8000/admin` খুললে নিজে থেকে `/admin/login`-এ চলে যায়
- [ ] ভুল password দিলে লাল error দেখায়
- [ ] সঠিক email-password দিলে Dashboard খোলে
- [ ] Dashboard-এর "Environment" table-এ দেখায়: Laravel 12.x, Livewire 4.x, Database mysql
- [ ] উপরে ডানে নামের উপর ক্লিক করলে "Log out" আছে, ক্লিক করলে login page-এ ফিরে যায়
- [ ] Logout-এর পর `/admin` আবার খুললে login page আসে
- [ ] Login অবস্থায় `/admin/login` খুললে সরাসরি Dashboard-এ নিয়ে যায়

### ধাপ ৭: Test চালাও
```bash
php artisan test
```
সবগুলো সবুজ (pass) হওয়ার কথা। এই test গুলো আলাদা অস্থায়ী database ব্যবহার করে, তোমার আসল `probashi_kormo` database-এ হাত দেয় না।

### ধাপ ৮: Code style ঠিক করে commit করো
```bash
./vendor/bin/pint
git add .
git commit -m "feat: add admin login, roles and access control"
```
(PowerShell-এ pint: `vendor\bin\pint`)

## সমস্যা হলে

| সমস্যা | সমাধান |
|---|---|
| `composer require` এ "requires php ^8.3" error | `:^6.0` ছাড়া দিয়েছ। `composer require spatie/laravel-permission:^6.0` দাও |
| `Super Admin not created` | `.env`-এ `ADMIN_EMAIL`/`ADMIN_PASSWORD` নেই বা password ১০ অক্ষরের কম। ঠিক করে `php artisan config:clear` তারপর `php artisan db:seed` |
| `Table 'roles' doesn't exist` | ধাপ ২-এর `vendor:publish` করা হয়নি, বা `php artisan migrate` বাকি |
| `Class "Spatie\Permission\Traits\HasRoles" not found` | ধাপ ২-এর `composer require` হয়নি |
| `php artisan test` এ "could not find driver" | XAMPP-এর `php.ini`-তে `extension=pdo_sqlite` আর `extension=sqlite3` এর সামনের `;` তুলে দাও, তারপর terminal নতুন করে খোলো |
| Test-এ `Vite manifest not found` | test-এ `withoutVite()` দেওয়া আছে, তাই হওয়ার কথা না। হলে আমাকে error-টা পাঠাও |
| Login দেওয়ার পর আবার login page-এ ফেরে | `.env`-এ `SESSION_DRIVER=database` থাকলে `php artisan migrate` হয়েছে কিনা দেখো (`sessions` table লাগে), `php artisan optimize:clear` দাও |
| `Too many login attempts` | ১ মিনিট অপেক্ষা করো |

## মনে রাখার বিষয়
- নতুন কর্মী (Admin/Staff) বানানোর page Step 07-এ আসবে। তার আগে শুধু Super Admin আছে।
- "Forgot password" এখনও নেই (mail setup দরকার)। পাসওয়ার্ড ভুলে গেলে এখন `php artisan tinker` দিয়ে বদলাতে হবে।

## পরের step (Step 04): Database design
Jobs, Countries, Training courses, Applications, Inquiries, Gallery, News, Success stories, Services-এর table নকশা (normalized), migration, model ও seed data। Step 03 যাচাই করে জানাও।
