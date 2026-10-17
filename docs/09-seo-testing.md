# Step 09 — SEO (dynamic sitemap) + Testing

## কী যোগ হলো
- `GET /sitemap.xml` (`SeoController@sitemap`): static পাতা + active Service/Country/Training + খোলা Job + published News। বন্ধ, মেয়াদ-শেষ, draft, ভবিষ্যতের তারিখের item আসে না। `Cache-Control: public, max-age=3600`
- `GET /robots.txt` (`SeoController@robots`): production-এ `Disallow: /admin` + `Sitemap:` লাইন; production ছাড়া (local/staging) সবকিছু `Disallow: /`
- Public layout: `<link rel="canonical">` (query string ছাড়া), Open Graph, Twitter card। ছবি না থাকলে logo
- News detail: `og:type=article`, cover ছবি থাকলে share image
- Home: `EmploymentAgency` JSON-LD (phone/email/address শুধু .env-এ থাকলে)
- Job detail: `JobPosting` JSON-LD (validThrough, দেশ, বেতন)। বন্ধ job-এ দেয় না
- `App\Support\Seo`: JSON-LD বানায়; `JSON_HEX_*` দিয়ে `</script>` ভেঙে বের হওয়া আটকায়
- Admin ও login layout-এ আগে থেকেই `noindex, nofollow` আছে

## করণীয় (জরুরি)
1. **`public/robots.txt` মুছে ফেলো।** ফাইল থাকলে web server route-এর আগে ওটাই দেয়
2. Production `.env`-এ `APP_ENV=production` আর আসল `APP_URL=https://...` (sitemap-এর লিংক এখান থেকে)
3. live হলে Google Search Console-এ `https://your-domain/sitemap.xml` জমা দাও
4. `php artisan optimize:clear`

## Test
`tests/Feature/SeoTest.php` (১০টা): sitemap XML ও static পাতা, শুধু public item, robots (non-production / production), canonical + OG, Organization, JobPosting (খোলা/বন্ধ), news share image, `</script>` escape।

চালাও: `php artisan test --filter=SeoTest`, তারপর পুরো `php artisan test`।

## জানা সীমাবদ্ধতা
- Sitemap-এ `lastmod` static পাতায় নেই (তথ্য নেই)
- ছবির `alt`/size audit, page speed (ছবি compress) এখনও বাকি
- `JobPosting`-এর `jobLocation` হলো কাজের দেশ; Google-এর rich result সবসময় overseas job দেখায় না
