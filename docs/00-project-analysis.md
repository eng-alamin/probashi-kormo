# Step 00 — Project Analysis (Probashi Kormo)

## ১. তোমার দেওয়া দুইটা zip-এ কী আছে

### ক) `ui-probashi-kormo-site.zip` — Public website
- মোট **40টা HTML page**, **Tailwind CSS 3** দিয়ে বানানো (compiled CSS: `assets/css/tailwind.css`)।
- Page গ্রুপ:
  - Main: index, about, services, training, jobs, countries, gallery, news, success-stories, contact, apply, terms, privacy-policy
  - Detail page: `job-*` (৬টা), `country-*` (৭টা), `training-*` (৮টা), `service-*` (৬টা)
- Brand color: green `#006a4e`, dark `#004d38`, red `#e0202f`; font: Poppins।
- `build-seo.py` আছে (sitemap/robots/OG tag বানায়) — Laravel-এ গেলে এটার কাজ dynamic sitemap নেবে।
- **Apply** আর **Contact** form এখন শুধু WhatsApp-এ যায় (`WA_NUMBER` placeholder), database-এ কিছু save হয় না।
  - Apply fields: name, phone, age, district, passport, education, country, experience, job, message
  - Contact fields: name, phone, country, trade, message

### খ) `ui-super-admin.zip` — Admin UI
- ১৭টা page + `style.css` + `app.js`; **Bootstrap 5.3.3** + Bootstrap Icons; dark/light theme।
- নামটা "Loop Super Admin" — এটা একটা **multi-tenant SaaS** admin template।
- সব data এখন fake/mock (`app.js`-এ), theme শুধু browser `localStorage`-এ।

## ২. Admin page mapping (কোনটা রাখব, কোনটা বদলাব)

| Admin template page | Probashi Kormo-এ কী হবে |
|---|---|
| dashboard, analytics | Dashboard / Reports (applications, jobs, inquiries-এর সংখ্যা) |
| users, roles | Staff Users, Roles & Permissions |
| support-inbox | Contact Messages / Inquiries |
| logs | Activity & Audit Log |
| notifications, settings, my-profile | রাখব (site settings সহ) |
| tenants | Applicants / Candidates list-এর layout হিসেবে ব্যবহার করব |
| subscriptions, billing, coupons, feature-flags, api-keys, integrations, onboarding | **দরকার নেই** — এক agency-র জন্য SaaS feature লাগে না |

নতুন admin module লাগবে: Jobs, Countries, Training Courses, Applications, Gallery, News, Success Stories, Services।

## ৩. Assumption (ভুল হলে বলো)
- এটা **একটা agency-র** system — multi-tenant না।
- Database: **MySQL**।
- Public site-এর মতোই Bangla/English content, ভবিষ্যতে দরকার হলে ভাষা যোগ হবে।

## ৪. একটা সিদ্ধান্ত বাকি আছে (Step 02-তে নেব)
Public site **Tailwind**, Admin **Bootstrap 5** — দুই CSS framework। বিকল্প:
1. **Public = Tailwind (আগের মতোই), Admin = Bootstrap 5**, আলাদা layout ও আলাদা CSS file। কাজ কম, design অক্ষত। *(আমার recommendation)*
2. Public site-ও Bootstrap 5-এ rewrite — ৪০ page-এর class বদলাতে হবে, সময় বেশি।

Step 01-এ এই সিদ্ধান্ত লাগে না, তাই এখন শুধু backend setup।

## ৫. Roadmap
দেখো `docs/PROGRESS.md`।
