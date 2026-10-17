# Step 05B-1 — Public pages: Services, Countries, Training (+ Home)

> 05B বড়, তাই ভাগ করছি। এই ভাগে database থেকে data নিয়ে তিন গ্রুপের page আর Home-এর আসল অংশ। Jobs, Gallery, News, Success Stories, About, Terms, Privacy পরের ভাগে (05B-2)।

## কী বানালাম

| Route | Name | Page |
|---|---|---|
| `/services` | `services` | Service তালিকা + 4-step process + FAQ |
| `/services/{slug}` | `services.show` | Service detail (What We Do, What You Need to Bring) |
| `/countries` | `countries` | Country তালিকা |
| `/countries/{slug}` | `countries.show` | Country detail (job sectors, contract info, extra info) |
| `/training` | `training` | Course তালিকা |
| `/training/{slug}` | `training.show` | Course detail (learn, career, eligibility, quick info) |
| `/` | `home` | Home-এ এখন database-এর service, country, course দেখায় |

নিয়ম:
- শুধু `is_active = true` item দেখায়। বন্ধ করা item-এর URL-এ গেলে 404, তালিকা থেকেও বাদ।
- তালিকার ঘর (`job_sectors`, `learn_points` ইত্যাদি) প্রতি লাইনে একটা আইটেম, `✔` দিয়ে সাজানো। ঘর ফাঁকা হলে ওই অংশ পুরো লুকানো থাকে।
- Duration, Fee, Class days, Certificate, Contract ফাঁকা থাকলে "Contact us" দেখায়। "Add here" আর দেখাবে না।
- Course fee দেখায় `৳ 12,000` ভাবে (টাকায় ধরা হয়েছে)।
- প্রতি page-এর meta description আসে item-এর `summary` থেকে।
- Design অক্ষত: static site-এর হুবহু Tailwind class, তাই **CSS নতুন করে build লাগবে না**।

## নতুন ফাইল
- `app/Support/Lines.php`
- `app/Livewire/Pages/{Services,Countries,Training}/{Index,Show}.php`
- `resources/views/livewire/pages/{services,countries,training}/{index,show}.blade.php`
- `resources/views/components/site/{banner,checklist,info-row,steps,faq,help-box,cta,chips}.blade.php`
- `tests/Feature/Public/PublicPagesTest.php`
- `docs/05b1-public-pages-guide.md`

## বদলানো ফাইল (replace)
- `routes/web.php`
- `app/Livewire/Pages/Home.php`
- `resources/views/livewire/pages/home.blade.php`
- `resources/views/layouts/site.blade.php` (per-page meta description)
- `docs/PROGRESS.md`

## করণীয় ধাপ
```bash
git status            # পরিষ্কার আছে কিনা
# zip extract করো (Replace জিজ্ঞাসা করলে Yes)
php artisan optimize:clear
php artisan route:list --name=services
php artisan test
./vendor/bin/pint
```
Browser-এ দেখো: `/`, `/services`, `/services/visa-processing`, `/countries`, `/countries/qatar`, `/training`, `/training/electrician`।

```bash
git add .
git commit -m "feat: public pages for services, countries and training"
```

## যাচাইয়ে খেয়াল করো
- Nav-এর Services / Training / Countries link এখন `#` না হয়ে আসল URL-এ যাবে।
- Admin থেকে এখনও data বদলানো যায় না (Step 07)। এখন বদলাতে চাইলে phpMyAdmin বা `php artisan tinker`।
- Home-এর stats (`500+ Workers Sent`, `10+ Countries`, `20+ Trades`, `100%`) আগের static site-এর হাতে লেখা সংখ্যা। আসল না হলে বদলাও বা সরাও। আমি ছুঁইনি।

## সমস্যা হলে
| সমস্যা | সমাধান |
|---|---|
| `Route [services.show] not defined` | `php artisan optimize:clear` |
| Page-এ style নেই | `public/site/css/tailwind.css` আছে কিনা দেখো |
| `Unable to resolve dependency [$service]` | Route parameter নাম (`{service}`) আর `mount(Service $service)` একই থাকতে হবে |
| তালিকা ফাঁকা | `php artisan db:seed` চালাও |
