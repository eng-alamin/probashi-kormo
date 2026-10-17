# Step 07E — Admin: Staff Users, Roles & Permissions

> আগে Step 07D যাচাই করে নাও (`php artisan test` সবুজ, Gallery/News/Stories ঠিক চলে, ছবি upload হয়)।

## কী বানালাম

| ফাইল | কাজ |
|---|---|
| `app/Livewire/Admin/Users/Index.php`, `Form.php` + view | Staff-এর তালিকা (search, role filter, Active switch, delete) ও নতুন/edit form |
| `app/Livewire/Admin/Roles/Index.php`, `Form.php` + view | তিনটা role-এর তালিকা ও permission টিক-মেট্রিক্স |
| `app/Support/StaffAccess.php` | নিরাপত্তার নিয়মগুলো এক জায়গায় (কে কাকে বদলাতে পারে, শেষ Super Admin) |
| `config/permissions.php` | **(replace)** পাতায় দেখানোর নাম (`labels`, `action_labels`) যোগ। বাকি আগের মতো |
| `resources/css/admin.css` | শেষে permission মেট্রিক্সের style |
| `routes/web.php` | `admin.users.*`, `admin.roles.*` |
| `tests/Feature/Admin/UsersRolesAdminTest.php` | স্বয়ংক্রিয় test |

নতুন migration বা seeder নেই। Sidebar-এর "Staff Users" ও "Roles & Permissions" আগেই ছিল, এখন আর "Soon" নয়।

## নিয়মগুলো

### Staff Users
- **নতুন account:** নাম, email, password (কমপক্ষে ১০ অক্ষর, অক্ষর + সংখ্যা, দুইবার লিখতে হয়), role, Active।
- **Edit:** password ফাঁকা রাখলে পুরনোটাই থাকে। Email ছোট হাতের অক্ষরে save হয়।
- **একজনের একটাই role:** Super Admin / Admin / Staff।
- **Active বন্ধ করলে:** সে আর ঢুকতে পারে না। আগে থেকে লগইন থাকলে তার পরের ক্লিকেই বেরিয়ে যায় (Step 03-এর পাহারাদার)।
- **Delete:** account মুছে যায়। শুধু সাময়িক বন্ধ করতে চাইলে Active switch ব্যবহার করো।

### নিরাপত্তা (লক-আউট ও ক্ষমতা বাড়ানো আটকাতে)
- **নিজের account:** নাম, email, password বদলানো যায়। কিন্তু নিজের role বদলানো, নিজেকে বন্ধ করা বা মোছা যায় না।
- **শেষ সক্রিয় Super Admin:** মোছা, বন্ধ করা বা role নামানো যায় না। নাহলে কেউ আর Roles বদলাতে পারত না।
- **Super Admin-এর account:** শুধু Super Admin ছুঁতে পারে। Admin-কে users পাতার অনুমতি দিলেও Super Admin-কে edit/বন্ধ/মোছা বা Super Admin role দেওয়া যায় না।

### Roles & Permissions
- **তিনটা role স্থির:** নতুন role বানানো বা মোছা যায় না (লগইনের নিয়ম এই তিনটার উপর দাঁড়িয়ে আছে)। বদলানো যায় Admin ও Staff কী কী করতে পারে।
- **Super Admin:** সবকিছু পারে, বদলানো যায় না।
- **মেট্রিক্স:** প্রতিটা section-এ View / Add / Edit / Delete টিক। Add, Edit বা Delete টিক দিলে Save-এর সময় View নিজে থেকে চালু হয়ে যায় (নাহলে তালিকা খোলাই যেত না)।
- **Reset to default:** টিকগুলো আগের ডিফল্টে ফেরায়, কিন্তু Save না চাপা পর্যন্ত বদলায় না।
- **Staff Users ও Roles section:** শুধু Super Admin এই দুটোর টিক বদলাতে পারে। নাহলে একজন Admin নিজের role-এ টিক দিয়ে নিজেকেই Super Admin-এর মতো করে ফেলতে পারত।
- **সাথে সাথে কাজ করে:** permission বদলালে ওই role-এর সবার উপর সাথে সাথে খাটে।
- Dashboard সবাই দেখে, তাই কোনো permission না থাকলেও Staff ঢুকতে পারে কিন্তু বাকি মেনু ফাঁকা দেখবে।

## করণীয় ধাপ

### ধাপ ১: Git পরিষ্কার আছে কিনা দেখো
```bash
git status
```

### ধাপ ২: Zip extract করো
Project root-এ, "Replace" জিজ্ঞাসা করলে **Yes**। বদলাবে: `routes/web.php`, `config/permissions.php`, `resources/css/admin.css`, `docs/PROGRESS.md`।

### ধাপ ৩: Build, cache, test
```bash
npm run build        # বা চালু থাকলে npm run dev
php artisan optimize:clear
php artisan test
```
Seeder আবার চালানোর দরকার নেই।

### ধাপ ৪: Browser-এ যাচাই (Super Admin দিয়ে লগইন)
- [ ] Sidebar-এ Staff Users ও Roles & Permissions আর "Soon" নয়
- [ ] নতুন Staff বানাও (দুর্বল password দিলে error আসে), ওই account দিয়ে অন্য browser-এ লগইন হয়
- [ ] তালিকায় নিজের নামের পাশে "You", নিজের সারিতে Active switch ও Delete নেই
- [ ] Edit-এ নিজের role dropdown বন্ধ
- [ ] একজন Staff-কে Active বন্ধ করো, সে খোলা পাতায় ক্লিক করলেই লগইনে ফেরে
- [ ] Roles → Staff edit: Gallery টিক দিয়ে Save, তারপর Staff দিয়ে ঢুকে Gallery মেনু দেখা যায়
- [ ] Edit-এ শুধু "Edit" টিক দিয়ে Save করলে ফিরে এসে "View"-ও টিক দেখায়
- [ ] Reset to default চাপলে টিক বদলায়, কিন্তু Save না দিলে আসল permission বদলায়নি
- [ ] Super Admin role-এ edit বোতাম নেই (URL-এ গেলে 403)
- [ ] (ঐচ্ছিক) Admin role-কে `users.*` দিয়ে একজন Admin দিয়ে ঢুকলে: Super Admin-এর সারিতে edit/delete নেই, Roles পাতায় Users/Roles section ধূসর

## সীমাবদ্ধতা
- এই sandbox-এ Laravel বসানো নেই, তাই test চালানো যায়নি। সব PHP ফাইলের syntax (`php -l`) যাচাই হয়েছে।
- Password reset ("Forgot password") এখনও নেই। কেউ ভুলে গেলে Super Admin Edit-এ নতুন password বসাবে।
- Password form-এর property হিসেবে browser ↔ server যায় (Livewire-এর স্বাভাবিক নিয়ম), তাই শুধু HTTPS-এ ব্যবহার করো।
- নতুন role বানানো যায় না (ইচ্ছা করে)। দরকার হলে লগইনের নিয়ম বদলে আলাদা step-এ করা যাবে।

## পরের step (08): Dashboard (real data) + Activity log
