# Step 07C — Admin: Training ও Services

> আগে Step 07B যাচাই করে নাও (`php artisan test` সবুজ, Jobs/Countries পাতা ঠিক চলে)।

## কী বানালাম

| ফাইল | কাজ |
|---|---|
| `app/Livewire/Admin/Trainings/Index.php`, `Form.php` + view | Course-এর তালিকা (search, Visible switch, delete) ও নতুন/edit form |
| `app/Livewire/Admin/Services/Index.php`, `Form.php` + view | Service-এর তালিকা ও form |
| `routes/web.php` | `admin.trainings.*`, `admin.services.*` |
| `tests/Feature/Admin/TrainingsServicesAdminTest.php` | স্বয়ংক্রিয় test (লুকালে public পাতা 404 হয় কিনাও দেখে) |

নতুন migration, seeder বা component নেই। 07B-র `x-admin.*` আর `Slug` আবার ব্যবহার হয়েছে।

## নিয়মগুলো

- **Course form:** Duration (সপ্তাহ), Fee (৳), Class days, Certificate ফাঁকা রাখলে public পাতায় "Contact us" দেখায়। Seed করা ৮টা course-এ এই চারটা এখনও ফাঁকা, আসল তথ্য এখান থেকে বসাতে পারবে।
- **তালিকার ঘর:** What you will learn, Career options, Who can join, Service-এর What we do / What to bring। সবগুলোতে প্রতি লাইনে একটা আইটেম।
- **Icon:** একটা emoji paste করলেই হয় (⚡, 🔧)।
- **Display order:** ছোট সংখ্যা আগে দেখায়।
- **Slug:** আগের মতোই। ফাঁকা রাখলে নাম থেকে, edit-এ আগেরটাই থাকে।
- **Visible switch:** বন্ধ করলে public লিংক 404 হয় (home ও তালিকা থেকেও সরে যায়)।
- **Delete:** এই দুই table-এর উপর অন্য table নির্ভর করে না, তাই সরাসরি মোছা যায়। সাময়িক সরাতে Visible switch ভালো।
- **Permission:** Staff এখানে ঢুকতে পারে না। Admin ও Super Admin পারে।

## করণীয় ধাপ

### ধাপ ১: Git পরিষ্কার আছে কিনা দেখো
```bash
git status
```

### ধাপ ২: Zip extract করো
Project root-এ, "Replace" জিজ্ঞাসা করলে **Yes**। বদলাবে: `routes/web.php`, `docs/PROGRESS.md`।

### ধাপ ৩: Test ও cache
```bash
php artisan optimize:clear
php artisan test
```

### ধাপ ৪: Browser-এ যাচাই (`php artisan serve` + `npm run dev`)
- [ ] Sidebar-এ Training ও Services আর "Soon" নয়
- [ ] Training-এ ৮টা, Services-এ ৬টা seed করা আইটেম দেখায়
- [ ] একটা course edit করে Duration ও Fee বসাও, public `/training/{slug}`-এ দেখায়
- [ ] নতুন service বানাও (What we do-তে দুই লাইন), public পাতায় দুটো আলাদা আইটেম হয়ে দেখায়
- [ ] Visible switch বন্ধ করে public লিংক খুললে 404, home page থেকেও সরে যায়
- [ ] ভুল slug (`Bad Slug!`) বা Duration `0` দিলে লাল error আসে
- [ ] Staff account দিয়ে menu-তে এই দুটো নেই, URL-এ 403
- [ ] মোবাইল width-এ form এক কলামে

## সীমাবদ্ধতা
- এই sandbox-এ Laravel বসানো নেই, তাই test চালানো যায়নি। শুধু PHP syntax যাচাই হয়েছে।
- Course-এর `image_path` form-এ নেই (public পাতাও ব্যবহার করে না)। ছবি upload Step 07D-তে আসবে।

## পরের step (07D): Gallery, News, Success Stories CRUD (ছবি upload সহ)
