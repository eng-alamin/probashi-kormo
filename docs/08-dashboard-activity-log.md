# Step 08 — Dashboard (আসল তথ্য) + Activity Log

> আগে Step 07E যাচাই করে নাও (`php artisan test` সবুজ, Staff Users ও Roles ঠিক চলে)।

## কী বানালাম

| ফাইল | কাজ |
|---|---|
| `app/Livewire/Admin/Dashboard.php` + `resources/views/livewire/admin/dashboard.blade.php` | **(replace)** Dashboard এখন database-এর আসল সংখ্যা দেখায় |
| `database/migrations/2026_10_20_000001_create_activity_logs_table.php` | নতুন `activity_logs` table |
| `app/Models/ActivityLog.php`, `app/Enums/ActivityEvent.php` | model ও ঘটনার ধরন (Created, Updated, Deleted…) |
| `app/Support/ActivityLogger.php` | log লেখার একমাত্র জায়গা (ব্যর্থ হলেও আসল কাজ আটকায় না) |
| `app/Observers/ActivityObserver.php` | model তৈরি/বদল/মোছা নিজে থেকে ধরে |
| `config/activity.php` | কোন model দেখা হবে, কত দিন log রাখা হবে |
| `app/Livewire/Admin/Logs/Index.php` + view | Activity Log পাতা (search, event ও staff filter) |
| `app/Console/Commands/PruneActivityLog.php` | `php artisan activity:prune` — পুরনো line মোছে |
| `app/Providers/AppServiceProvider.php` | **(replace)** observer ও sign-in/sign-out listener চালু |
| `app/Livewire/Admin/{Applications,Inquiries,Jobs,Services,Trainings}/Index.php` | **(replace)** delete এখন model দিয়ে (নিচে কারণ দেখো) |
| `app/Livewire/Admin/Roles/Form.php`, `Users/Form.php` | **(replace)** permission ও role বদলের log |
| `config/permissions.php`, `config/admin_menu.php` | **(replace)** নতুন `logs.view` permission, মেনুতে `can` |
| `routes/web.php`, `routes/console.php` | **(replace)** `/admin/logs` route, প্রতিদিন prune-এর schedule |
| `tests/Feature/Admin/DashboardTest.php`, `ActivityLogTest.php` | স্বয়ংক্রিয় test |

## Dashboard

- **চারটা card:** Applications (মোট, নতুন কয়টা, এই সপ্তাহে কয়টা), Open Jobs, Training Courses, New Messages। নিচের ছোট লেখায় ক্লিক করলে ওই তালিকা খোলে।
- **Recent applications** (শেষ ৫টা), **Applications by status** (প্রতিটা status-এর সংখ্যা ও শতাংশ), **Last 7 days** (দিনভিত্তিক বার)।
- **Recent messages** (শেষ ৫টা) আর **Recent activity** (শেষ ৬টা)।
- **Permission মেনে দেখায়:** যে section খোলার অনুমতি নেই, তার card/তথ্য বানানোই হয় না। যেমন Staff শুধু Applications ও Messages দেখে। কিছুই দেখার না থাকলে "Welcome" বার্তা আসে।
- Environment table আগের মতোই, শুধু local মেশিনে।

## Activity Log

### কী কী লেখা হয়
- Admin panel-এ **লগইন থাকা কেউ** যা বানায়, বদলায় বা মোছে (Applications, Inquiries, Jobs, Countries, Training, Services, Gallery, News, Success Stories, Staff Users)।
- Status বদল আলাদা করে: `Changed status of application "Rahim" from New to Contacted`।
- Staff-এর role বদল, Admin/Staff role-এর permission বদল (কোনটা যোগ, কোনটা বাদ)।
- Staff-এর Sign in / Sign out।

### কী লেখা হয় না
- **মান (value) কখনও না, শুধু ঘরের নাম।** যেমন `changed: password`, `changed: admin note`। তাই ফোন নম্বর, নোট বা password log-এ যায় না।
- লগইন ছাড়া কিছু: seeder, tinker, আর public site-এর Apply/Contact form।
- নতুন inquiry খুললে নিজে থেকে "Read" হয়, এটা log হয় না। কিন্তু হাতে status বদলালে হয়।
- ভুল password দিয়ে লগইন চেষ্টা (সীমাবদ্ধতা দেখো)।

### কে দেখতে পারে
- নতুন permission `logs.view`। **ডিফল্টে শুধু Super Admin**। Admin চাইলে Roles পাতা থেকে Super Admin তাকে দিতে পারে (Admin নিজে নিজেকে দিতে পারে না, Users/Roles-এর মতোই)।
- Log পাতায় মোছা বা বদলানোর কোনো বোতাম নেই।

### কত দিন থাকে
- ডিফল্ট ১৮০ দিন। `.env`-এ `ACTIVITY_LOG_DAYS=365` দিয়ে বদলানো যায়।
- `php artisan activity:prune` হাতে চালানো যায় (`--days=30` দিলে ৩০ দিনের পুরনো যাবে)। প্রতিদিন নিজে থেকে চলার schedule আছে, কিন্তু তার জন্য server-এ cron লাগবে (নিচে)।

## কেন ৫টা list পাতা বদলাতে হলো
Applications, Inquiries, Jobs, Services ও Training-এর তালিকা আগে `Model::whereKey($id)->delete()` দিয়ে মুছত। এই পদ্ধতি model-এর "ঘটনা" ছাড়াই সরাসরি query চালায়, তাই Activity Log কিছু টের পেত না। এখন `Model::find($id)?->delete()`। কাজ হুবহু এক, শুধু log ধরা পড়ে। বাকি পাতাগুলো (Countries, Gallery, News, Stories, Users) আগেই model দিয়ে মুছছিল।

## Database নিয়ে একটা সিদ্ধান্ত (normalization)
`activity_logs`-এ `user_id` আছে (foreign key, user মুছলে `NULL`) এবং সাথে `actor_name` (নামের কপি)। এটা ইচ্ছা করে: log হলো ইতিহাস। কোনো staff-এর account মুছে গেলে "কে করেছিল" প্রশ্নের উত্তর যেন হারিয়ে না যায়। বাকি table আগের মতোই normalized।

## চালু করার ধাপ

```bash
php artisan migrate
php artisan db:seed --class=PermissionSeeder
php artisan optimize:clear
php artisan test
```

- `PermissionSeeder` না চালালে `logs.view` permission তৈরি হবে না (Super Admin তবুও দেখতে পারবে, কিন্তু Roles পাতার টিক-মেট্রিক্সে "Activity Log" সারিটা কাজ করবে না)।
- নতুন JS/CSS নেই, তাই `npm run build` লাগবে না।

### Server-এ প্রতিদিনের prune (production-এর আগে)
Hosting-এর cron-এ এই লাইন বসাও (প্রতি মিনিটে):

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Local-এ দরকার নেই। চাইলে `php artisan schedule:work` চালিয়ে রাখো, বা `php artisan activity:prune` হাতে চালাও।

## হাতে যাচাই (browser-এ)
1. Super Admin দিয়ে লগইন করো → Dashboard-এ চারটা card-এ আসল সংখ্যা, নিচে Recent activity-তে "Signed in" দেখাবে।
2. একটা Job বানাও, title বদলাও, Visible বন্ধ করো, মুছে দাও → **Activity Log** পাতায় চারটা line।
3. একটা Application-এর status বদলে নোট লেখো → line-এ status বদল দেখাবে, নোটের লেখা **দেখাবে না**।
4. Roles → Staff-এ একটা টিক দিয়ে Save → "added …" line।
5. Staff account দিয়ে লগইন করো → Dashboard-এ শুধু Applications ও Messages, মেনুতে Activity Log নেই, `/admin/logs` খুললে 403।
6. Activity Log-এ event filter ও search চালিয়ে দেখো।

## Test
- `DashboardTest`: আসল সংখ্যা, শেষের তালিকা, ৭ দিনের চার্ট, ফাঁকা database, Staff-এর সীমিত দেখা, activity panel-এর permission, "Welcome" অবস্থা।
- `ActivityLogTest`: তৈরি/বদল/toggle/মোছা, লগইন ছাড়া কিছু লেখা হয় না, status বদলের বর্ণনা ও নোট না-লেখা, নতুন inquiry খোলা log হয় না, ৫টা list পাতার delete, password-এর মান লেখা হয় না, log ব্যর্থ হলেও কাজ আটকায় না, role ও permission বদল, sign in/out, পাতার access ও filter, prune।

## সীমাবদ্ধতা
- **ভুল password দিয়ে লগইন চেষ্টা log হয় না।** ইচ্ছা করে: নাহলে অন্যের email লিখে log ভরানো যেত। এখনকার ৫ বারে block-এর নিয়ম আগের মতোই আছে।
- Log-এর লেখা ইংরেজিতে।
- Remember-me cookie দিয়ে আবার ঢুকলেও "Signed in" line আসে।
- একসাথে ২০টা Gallery ছবি upload করলে ২০টা "Created gallery item" line হয়।
- Settings মেনু এখনও "Soon" (এই step-এর বাইরে)।
