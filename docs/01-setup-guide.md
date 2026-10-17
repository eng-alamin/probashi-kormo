# Step 01 — Laravel 12 + Livewire 4 + MySQL Setup

> সব command VS Code-এর **Terminal** (`Ctrl + ~`) এ চালাবে। Windows হলে PowerShell বা Git Bash ঠিক আছে।

## ধাপ ১: আগে tool গুলো আছে কিনা দেখো

```bash
php -v
composer -V
node -v
npm -v
git --version
```

- `php -v` এ **8.2 বা তার উপরে** দেখাতে হবে (Laravel 12-এর শর্ত)।
- MySQL লাগবে: XAMPP / Laragon / MySQL Server যেকোনোটা চালু রাখো।

## ধাপ ২: Laravel 12 project বানাও

```bash
cd D:\Projects
composer create-project laravel/laravel:^12.0 probashi-kormo
cd probashi-kormo
code .
```

**কেন `:^12.0`?** এখন Laravel 13 ও বের হয়ে গেছে। `:^12.0` না দিলে নতুন version আসবে, তোমার দরকার Laravel 12।

> তোমার পুরনো static site-এর folder নামও `probashi-kormo` হলে সেটার নাম বদলে `probashi-kormo-static` করে নাও, নাহলে নাম মিলে যাবে।

## ধাপ ৩: Livewire 4 install করো

VS Code-এর নতুন terminal-এ (project folder-এর ভেতরে):

```bash
composer require livewire/livewire:^4.0
```

**কেন `:^4.0`?** major version পিন করে রাখলে ভবিষ্যতে `composer update` দিলে হঠাৎ অন্য major version এসে code ভাঙবে না।

> Livewire 4-এ কিছু নতুন নিয়ম আছে (যেমন নতুন component তৈরির ধরন, route লেখার নতুন উপায়)। Step 02 থেকে আমি Livewire 4-এর নিয়মেই code লিখব, তাই Livewire 3-এর পুরনো tutorial দেখে code মেলাতে গেলে গরমিল হতে পারে।

## ধাপ ৪: MySQL database বানাও

phpMyAdmin থেকে `probashi_kormo` নামে database বানাও, collation: `utf8mb4_unicode_ci`।
অথবা terminal থেকে:

```bash
mysql -u root -p -e "CREATE DATABASE probashi_kormo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## ধাপ ৫: `.env` ফাইল ঠিক করো

`.env` খুলে এই লাইনগুলো বদলাও।

**সাবধান:** Laravel 12-এ MySQL-এর লাইনগুলো শুরুতে `#` দিয়ে comment করা থাকে, `#` তুলে দিতে হবে। না তুললে Laravel চুপচাপ SQLite ব্যবহার করবে।

```env
APP_NAME="Probashi Kormo"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=probashi_kormo
DB_USERNAME=root
DB_PASSWORD=
```

`DB_PASSWORD` তোমার MySQL password দাও (XAMPP/Laragon-এ সাধারণত ফাঁকা)।
`.env` ফাইল zip-এ দিইনি কারণ এতে `APP_KEY` আর password থাকে, ওটা কারো সাথে share করতে নেই।

## ধাপ ৬: Timezone Dhaka করো

`config/app.php` খুলে এই লাইনটা খুঁজে:

```php
'timezone' => 'UTC',
```

বদলে লেখো:

```php
'timezone' => 'Asia/Dhaka',
```

কেন: এক দেশের agency, তাই সময় সরাসরি ঢাকার সময়ে save হবে, ঝামেলা কম। পুরো `config/app.php` replace করছি না, কারণ framework-এর ফাইল, ভুলে একটা লাইন বাদ গেলে app ভাঙতে পারে।

## ধাপ ৭: SQLite-এর ফালতু ফাইল মোছো

project বানানোর সময় Laravel নিজে SQLite database বানিয়ে ফেলে। এখন MySQL ব্যবহার করছি, তাই মুছে দাও:

```bash
# Windows PowerShell
Remove-Item database\database.sqlite

# Git Bash / Mac / Linux
rm database/database.sqlite
```

## ধাপ ৮: Migration চালাও (MySQL-এ table বানাবে)

```bash
php artisan config:clear
php artisan migrate
```

সফল হলে `users`, `sessions`, `cache`, `jobs` ইত্যাদি table তৈরি হবে। phpMyAdmin-এ গিয়ে দেখে নিও।

## ধাপ ৯: ঠিকমতো হয়েছে কিনা যাচাই করো

```bash
php artisan about
```

এখানে দেখবে:
- Laravel Version: **12.x**
- PHP Version: **8.2+**
- Database: **mysql**
- Livewire: **4.x** (তালিকায় আসবে)

তারপর server চালাও:

```bash
php artisan serve
```

browser-এ `http://127.0.0.1:8000` খুললে Laravel-এর welcome page আসবে।

## ধাপ ১০: এই zip-এর ফাইলগুলো বসাও

এই zip extract করে project-এর **root** folder-এ রাখো (`artisan` ফাইলটা যেখানে আছে)। এগুলো নতুন ফাইল, কিছু overwrite হবে না:

| ফাইল | কাজ |
|---|---|
| `pint.json` | Code style PSR-12 করে |
| `.vscode/extensions.json` | VS Code recommended extension তালিকা |
| `docs/*.md` | Project-এর documentation |

Code style ঠিক করতে যেকোনো সময়:

```bash
./vendor/bin/pint
```

(Windows PowerShell-এ: `vendor\bin\pint`)

## ধাপ ১১: Git শুরু করো

```bash
git init
git add .
git commit -m "chore: fresh Laravel 12 with Livewire 4 and MySQL"
```

`.env` Laravel-এর `.gitignore`-এ আগে থেকেই আছে, তাই commit হবে না।

## ধাপ ১২: তোমার UI zip দুইটা রাখার জায়গা

দুই zip extract করে project-এর **বাইরে** রাখো, যেমন:

```
D:\Projects\
├── probashi-kormo\            ← Laravel project
└── probashi-kormo-ui\
    ├── site\                  ← ui-probashi-kormo-site.zip
    └── admin\                 ← ui-super-admin.zip
```

কেন বাইরে: নাহলে ভুল করে server-এ upload হয়ে যেতে পারে, আর project-ও এলোমেলো হয়।

## সমস্যা হলে

| সমস্যা | সমাধান |
|---|---|
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL চালু আছে কিনা দেখো, `DB_HOST`/`DB_PORT` ঠিক কিনা দেখো |
| `Unknown database 'probashi_kormo'` | ধাপ ৪ করা হয়নি |
| `Access denied for user 'root'` | `.env`-এ `DB_PASSWORD` ভুল |
| `php` command পাওয়া যায় না | PHP-র folder System PATH-এ যোগ করো (XAMPP: `C:\xampp\php`) |
| `.env` বদলালেও পুরনো মান দেখাচ্ছে | `php artisan config:clear` চালাও |

## Step 01 শেষ হলে আমাকে জানাও

`php artisan about`-এর output পাঠালে আমি দেখে নেব সব ঠিক আছে কিনা, তারপর **Step 02 (Frontend foundation + layout)** শুরু করব।
