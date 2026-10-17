# Step 02 — Frontend Foundation (Layout + Vite + Bootstrap 5)

> এই step-এ কোনো database বা login নেই। শুধু দুইটা "কাঠামো" (layout) বানানো হলো: একটা public website-এর, একটা admin panel-এর।

## কী কী সিদ্ধান্ত নিয়েছি (আর কেন)

| বিষয় | সিদ্ধান্ত | কারণ |
|---|---|---|
| Public site CSS | আগের **Tailwind CSS**, তৈরি করা `tailwind.css` ফাইল হিসেবে `public/site/`-এ | ৪০ page rewrite করতে হবে না, design অক্ষত থাকে। Laravel-এ Tailwind build চালানোর ঝামেলাও নেই |
| Admin CSS | **Bootstrap 5** (npm + Vite) | তোমার admin UI-র template Bootstrap 5-এ বানানো |
| Livewire component | **Class-based** (`app/Livewire/...`) | Logic আলাদা PHP class-এ থাকে, test ও maintain সহজ। Livewire 4-এ এটাও পুরোপুরি সমর্থিত |
| Route | `Route::livewire()` | Livewire 4-এর নতুন ও recommended নিয়ম |
| Layout জায়গা | `resources/views/layouts/` | Livewire 4-এর default (`layouts::` namespace) |

## Livewire 4 এর যে নিয়মগুলো এখন থেকে মানব
- Page-এর route: `Route::livewire('/path', ComponentClass::class)`
- Layout বেছে নেওয়া: component class-এর উপরে `#[Layout('layouts::admin')]`
- Page-এর title: `#[Title('Dashboard')]`
- Blade-এ component বসালে tag অবশ্যই বন্ধ করতে হবে: `<livewire:name />` (শুধু `<livewire:name>` লিখলে কাজ করবে না)

## এই zip-এ কী আছে

| ফাইল | কাজ |
|---|---|
| `vite.config.js` | Vite-কে বলে admin CSS/JS build করতে (**পুরনো ফাইল replace হবে**) |
| `routes/web.php` | `/` আর `/admin` route (**পুরনো ফাইল replace হবে**) |
| `config/site.php` | Website-এর নাম, ফোন, ইমেইল, WhatsApp, menu |
| `config/admin_menu.php` | Admin sidebar-এর menu |
| `app/Support/SafeRoute.php` | Page না থাকলে link `#` দেখায়, থাকলে আসল link |
| `app/Livewire/Pages/Home.php` + view | Home page |
| `app/Livewire/Admin/Dashboard.php` + view | Admin dashboard |
| `resources/views/layouts/site.blade.php` | Public layout (header, footer, WhatsApp বাটন) |
| `resources/views/layouts/admin.blade.php` + `partials/` | Admin layout (sidebar, topbar, mobile tab bar) |
| `resources/css/admin.css`, `resources/js/admin.js` | Admin-এর style ও behaviour |
| `public/site/` | Public site-এর `tailwind.css`, `main.js`, `logo.jpg` |
| `docs/` | এই ডকুমেন্টেশন |

## করণীয় ধাপ

### ধাপ ১: আগে Git পরিষ্কার আছে কিনা দেখো

```bash
git status
```

"nothing to commit" দেখালে ঠিক আছে। না দেখালে আগে commit করে নাও। কারণ এই zip দুইটা পুরনো ফাইল (`vite.config.js`, `routes/web.php`) বদলে দেবে, ভুল হলে Git দিয়ে ফেরত আনা যাবে।

### ধাপ ২: npm package বসাও আর অপ্রয়োজনীয়টা সরাও

```bash
npm install bootstrap@5.3 bootstrap-icons@1.11
npm uninstall tailwindcss @tailwindcss/vite
```

Laravel 12 নতুন project-এ Tailwind বসিয়ে দেয়। আমরা Laravel-এর ভেতরে Tailwind build ব্যবহার করছি না, তাই সরিয়ে দিলাম।

### ধাপ ৩: zip extract করো

Zip-টা project-এর **root** folder-এ extract করো (`artisan` ফাইলটা যেখানে আছে)। "Replace files" জিজ্ঞাসা করলে **Yes**।

তারপর দেখো:

```bash
git status
```

Modified: `vite.config.js`, `routes/web.php`, `package.json`, `package-lock.json`। বাকি সব নতুন (untracked)।

### ধাপ ৪: `.env` ফাইলে তোমার agency-র আসল তথ্য দাও

`.env` ফাইলের একদম নিচে যোগ করো (নিজের আসল নম্বর/ঠিকানা দিয়ে):

```env
SITE_PHONE="+880 1XXX-XXXXXX"
SITE_EMAIL="info@probashikormo.com"
SITE_ADDRESS="Your office address, Bangladesh"
SITE_WHATSAPP=8801XXXXXXXXX
```

`SITE_WHATSAPP` তে দেশের কোড সহ নম্বর, `+` বা space ছাড়া (যেমন `8801712345678`)।

### ধাপ ৫: Cache পরিষ্কার করো

```bash
php artisan optimize:clear
```

### ধাপ ৬: দুইটা terminal-এ project চালাও

VS Code-এ `Ctrl + Shift + ~` দিয়ে দ্বিতীয় terminal খুলে:

```bash
# Terminal 1
php artisan serve

# Terminal 2 (এটা চালু রাখতে হবে, নাহলে admin-এর style আসবে না)
npm run dev
```

(একটা command-এ দুইটাই চালাতে চাইলে: `composer run dev`)

### ধাপ ৭: ঠিকমতো কাজ করছে কিনা যাচাই করো

**`http://127.0.0.1:8000`** (Public Home):
- [ ] সবুজ hero section, logo, "Apply Now" বাটন দেখাচ্ছে
- [ ] উপরে menu আছে, Home highlighted
- [ ] নিচে footer আছে, WhatsApp বাটন ডানে নিচে
- [ ] Mobile-এ (browser ছোট করে) ☰ বাটনে menu খোলে
- [ ] About, Services ইত্যাদি link-এ ক্লিক করলে কিছু হয় না (`#`), কারণ ওই page এখনও বানাইনি, এটা ঠিক আছে

**`http://127.0.0.1:8000/admin`** (Admin):
- [ ] ডার্ক theme, বাঁ দিকে sidebar
- [ ] Dashboard ছাড়া বাকি menu-তে ছোট "Soon" লেখা, ক্লিক করা যায় না
- [ ] উপরে 🌙 বাটনে theme dark/light বদলায়, refresh করলেও মনে থাকে
- [ ] ☰ বাটনে sidebar ছোট/বড় হয় (mobile-এ drawer হিসেবে খোলে)
- [ ] ৪টা কার্ড (সংখ্যা ০) আর নিচে "Environment (local only)" table: Laravel 12.x, Livewire 4.x, Database mysql, Timezone Asia/Dhaka

### ধাপ ৮: Code style ঠিক করো ও commit করো

```bash
./vendor/bin/pint
git add .
git commit -m "feat: add public and admin layouts with Vite and Bootstrap 5"
```

(Windows PowerShell-এ pint: `vendor\bin\pint`)

## সমস্যা হলে

| সমস্যা | সমাধান |
|---|---|
| `Vite manifest not found` | `npm run dev` চালু নেই। চালু করো, অথবা একবার `npm run build` দাও |
| `Call to undefined method ...::livewire()` বা `No hint path defined for [layouts]` | Livewire 4 বসেনি। `composer show livewire/livewire` দিয়ে version দেখো। 4.x না হলে `composer require livewire/livewire:^4.0` |
| `Class "App\Livewire\Pages\Home" not found` | `composer dump-autoload` চালাও, তারপর `php artisan optimize:clear` |
| Public page-এ style নেই | `public/site/css/tailwind.css` আছে কিনা দেখো। `php artisan serve` project-এর root থেকে চালিয়েছ কিনা দেখো |
| Admin-এ icon/ style নেই | `npm run dev` চালু আছে কিনা দেখো। `npm install bootstrap@5.3 bootstrap-icons@1.11` করা হয়েছে কিনা দেখো |
| `.env` পরিবর্তন কাজ করছে না | `php artisan optimize:clear` |

## জরুরি সতর্কতা
- `/admin` এখন **login ছাড়া খোলা**। Step 03-এ login বসবে। তার আগে কোনো live server-এ upload করো না।
- Dashboard-এর "Environment" table শুধু local machine-এ দেখায়, live server-এ নিজে থেকেই লুকানো থাকবে।

## পরের step (Step 03): Admin Login + Roles
- Admin login page (Livewire), logout, session security
- Role ও permission (Super Admin, Admin, Staff)
- `/admin` সুরক্ষিত করা

Step 02 যাচাই করে আমাকে জানাও (দুইটা page ঠিকমতো দেখা গেছে কিনা, বা error থাকলে তার লেখা/screenshot), তারপর Step 03 শুরু করব।
