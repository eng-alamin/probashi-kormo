# Step 06b — Placeholder সমস্যা ঠিক করা (সাইটের যোগাযোগ তথ্য)

> সমস্যা: `.env`-এ আসল তথ্য না দিলে জনসমক্ষে `+880 1XXX-XXXXXX`, `Your office address` দেখাত। আমি আপনার আসল নম্বর জানি না, তাই code এভাবে বদলেছি: **ফাঁকা বা ভুয়া হলে ওই অংশ লুকিয়ে যায়, কখনও placeholder দেখায় না।**

## কী বদলালাম
- `config/site.php`: phone, email, address, whatsapp-এর ভুয়া default সরানো (এখন ফাঁকা)।
- `App\Support\Site`: প্রতিটা তথ্য যাচাই করে। ফাঁকা, `XXX` ধরনের লেখা, ভুল email, ভুল WhatsApp নম্বর হলে `null`।
- Topbar, Footer, Contact page, Job/Help box, Apply-এর ধন্যবাদ পাতা: শুধু ঠিক তথ্যই দেখায়। Phone ক্লিক করলে কল (`tel:`), email ক্লিক করলে mail (`mailto:`)।
- **WhatsApp ভাসমান বোতাম** ঠিক নম্বর না থাকলে দেখাই যায় না (আগে ভাঙা `wa.me/8801XXXXXXXXX` লিংকে যেত)।
- WhatsApp নম্বর দেশের কোড দিয়ে হতে হবে, শুরুতে `0` নয়: `8801712345678`। `+` বা স্পেস থাকলে নিজে পরিষ্কার করে নেয়।
- নতুন command: `php artisan site:check` — কোন তথ্য বাকি তা টেবিলে দেখায়। required চারটার (phone, email, address, WhatsApp) একটাও বাকি থাকলে "failed" হয়, তাই deploy script-এও বসানো যায়।

## আপনাকে করতে হবে
1. `docs/env-site-example.txt` খুলে আপনার `.env`-এ পেস্ট করুন, মান বদলান।
2. ```bash
   php artisan config:clear
   php artisan site:check
   ```
   সব `OK` হলে তৈরি। (Production-এ `config:cache` চালালে `.env` বদলের পর আবার `config:clear` লাগে।)

## ফাইল
নতুন: `app/Support/Site.php`, `app/Console/Commands/SiteCheck.php`, `tests/Feature/SiteSettingsTest.php`, `docs/env-site-example.txt`, `docs/06b-site-settings.md`
বদলানো: `config/site.php`, `resources/views/layouts/site.blade.php`, `components/site/help-box.blade.php`, `livewire/pages/jobs/show.blade.php`, `livewire/pages/forms/{apply,contact}.blade.php`, `docs/PROGRESS.md`

## সীমাবদ্ধতা
- Facebook লিংক আগের static site থেকে নেওয়া, ঠিক আছে কিনা একবার দেখে নিন।
- এই command শুধু অবস্থা দেখায়, `.env` নিজে বদলায় না।
