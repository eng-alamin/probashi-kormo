# Step 07A — Admin: Applications, Inquiries + Permissions

> আগে Step 06 তোমার মেশিনে যাচাই করে নাও (`/apply` আর `/contact` form database-এ save হয় কিনা)। এই step-এর data ওখান থেকেই আসে।

## কী বানালাম

Admin panel-এ দুইটা module চালু হলো, আর সব module-এর জন্য permission-এর কাঠামো তৈরি হলো।

| ফাইল | কাজ |
|---|---|
| `config/permissions.php` | সব module-এর permission (`applications.view` ...) আর Admin/Staff-এর default |
| `database/seeders/PermissionSeeder.php` | Permission বানায়, Admin ও Staff role-এ বসায় (`DatabaseSeeder` আপডেট) |
| `app/Livewire/Admin/Applications/Index.php`, `Show.php` + view | আবেদনের তালিকা (search, status ও দেশ filter, pagination, delete) আর বিস্তারিত পাতা (status বদল, internal note) |
| `app/Livewire/Admin/Inquiries/Index.php`, `Show.php` + view | বার্তার তালিকা ও বিস্তারিত পাতা |
| `app/Support/Phone.php` | phone থেকে WhatsApp লিংক (`01712345678` → `wa.me/8801712345678`) |
| `resources/views/pagination/admin.blade.php` | admin-এর নিজস্ব pagination |
| `routes/web.php` | `admin.applications.*`, `admin.inquiries.*` route (`can:` middleware সহ) |
| `config/admin_menu.php`, sidebar, tabbar | প্রতিটা menu item-এ `can` — permission না থাকলে menu-তে দেখায় না |
| `resources/css/admin.css`, `resources/js/admin.js` | toolbar/pager style, redirect-এর পর toast |
| `tests/Feature/Admin/ApplicationsInquiriesTest.php` | স্বয়ংক্রিয় test |

## কে কী পারে (default)

| Role | Applications / Inquiries | বাকি module |
|---|---|---|
| Super Admin | সব | সব (`Gate::before`) |
| Admin | দেখা, বদলানো, মোছা | সব, শুধু Staff Users ও Roles ছাড়া |
| Staff | দেখা ও বদলানো (মোছা যায় না) | কিছু না |

## নিরাপত্তার বিষয়
- Route-এ `can:` middleware আছে, আর Livewire-এর প্রতিটা action-এও (`save`, `delete`) আলাদা করে `authorize()` হয়। Livewire-এর বাটন ক্লিক আলাদা request, তাই শুধু page খোলার সময় চেক যথেষ্ট না।
- Status শুধু enum-এর মান নিতে পারে, ভুল মান validation-এ আটকায়।
- Search-এ `%` ও `_` escape করা হয়।
- নতুন inquiry খুললে "Read" হয়, কিন্তু শুধু যার `inquiries.update` আছে তার ক্ষেত্রে।
- Admin panel-এ `wire:navigate` ব্যবহার করিনি। ওটা body বদলে দেয়, তাতে sidebar/theme-এর JS ভেঙে যেত।

## করণীয় ধাপ

### ধাপ ১: Git পরিষ্কার আছে কিনা দেখো
```bash
git status
```

### ধাপ ২: Zip extract করো
Project root-এ extract করো, "Replace" জিজ্ঞাসা করলে **Yes**। বদলাবে: `routes/web.php`, `config/admin_menu.php`, `database/seeders/DatabaseSeeder.php`, `resources/css/admin.css`, `resources/js/admin.js`, `resources/views/layouts/admin.blade.php`, sidebar ও tabbar partial।

### ধাপ ৩: Permission তৈরি করো
```bash
php artisan optimize:clear
php artisan db:seed --class=PermissionSeeder
```
আবার চালালেও আগের কোনো permission মোছে না।

### ধাপ ৪: Test
```bash
php artisan test
```

### ধাপ ৫: Browser-এ যাচাই (`php artisan serve` + `npm run dev`)
- [ ] Sidebar-এ Applications আর Inquiries আর "Soon" নয়, ক্লিক করা যায়
- [ ] `/apply` দিয়ে একটা আবেদন পাঠাও, `/admin/applications`-এ দেখা যায়
- [ ] Search, status filter, দেশ filter কাজ করে
- [ ] আবেদন খুলে status বদলে Save করলে toast আসে, "Last handled by" দেখায়
- [ ] `/contact` দিয়ে বার্তা পাঠাও, খুললে status "Read" হয়
- [ ] Staff account দিয়ে ঢুকলে Delete বাটন নেই
- [ ] মোবাইল width-এ তালিকা card আকারে দেখায়

## সমস্যা হলে

| সমস্যা | সমাধান |
|---|---|
| সব page-এ 403 | `php artisan db:seed --class=PermissionSeeder` দাও, তারপর `php artisan permission:cache-reset` |
| Menu-তে Applications "Soon" দেখায় | `php artisan optimize:clear` |
| Pagination বাটন কাজ করে না | `npm run dev` চালু আছে কিনা দেখো, browser hard refresh |

## সীমাবদ্ধতা
- এই sandbox-এ Laravel/vendor বসানো নেই, তাই test চালানো যায়নি। শুধু PHP ও JS syntax যাচাই হয়েছে। Step 07A-র `php artisan test` তোমার মেশিনে চালিয়ে ফল জানিও।
- Email/notification এখনও নেই, নতুন আবেদনে কেউ জানতে পারে না (Step 08-এর পর দেখব)।

## পরের step (07B): Jobs ও Countries CRUD
