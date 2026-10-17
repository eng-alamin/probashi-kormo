# Step 07D — Admin: Gallery, News, Success Stories (ছবি upload সহ)

> আগে Step 07C যাচাই করে নাও (`php artisan test` সবুজ, Training/Services পাতা ঠিক চলে)।

## কী বানালাম

| ফাইল | কাজ |
|---|---|
| `app/Support/ImageStore.php` | সব ছবি upload এক জায়গায়: নিয়ম (JPG/PNG/WebP, সর্বোচ্চ 4 MB), save, মোছা, preview |
| `app/Livewire/Admin/Gallery/Index.php`, `Form.php` + view | ছবির তালিকা (search, category filter, Visible switch, delete) ও একসাথে অনেক ছবি upload |
| `app/Livewire/Admin/News/Index.php`, `Form.php` + view | খবরের তালিকা (search, status filter, Published switch, delete) ও লেখা/edit form (cover ছবি সহ) |
| `app/Livewire/Admin/Stories/Index.php`, `Form.php` + view | Success story-র তালিকা ও form (গোল photo সহ) |
| `resources/views/components/admin/image.blade.php` | নতুন reusable `x-admin.image` (ছবি বাছা, preview, বর্তমান ছবি, "Remove" টিক) |
| `resources/css/admin.css` | শেষে Upload/thumbnail অংশ যোগ হয়েছে |
| `routes/web.php` | `admin.gallery.*`, `admin.news.*`, `admin.stories.*` |
| `resources/views/livewire/pages/news/index.blade.php` | খবরের তালিকায় cover ছবি দেখায় (থাকলে) |
| `tests/Feature/Admin/GalleryNewsStoriesAdminTest.php` | স্বয়ংক্রিয় test |

নতুন migration বা seeder **নেই**। Permission (`gallery.*`, `news.*`, `stories.*`) আর sidebar menu আগেই তৈরি ছিল, শুধু route না থাকায় "Soon" দেখাত।

## ছবি upload-এর নিয়ম

- **কোথায় যায়:** `storage/app/public/gallery`, `news`, `stories` (ফাইলের নাম random, তাই একই নামে ওভাররাইট হয় না)। database-এ শুধু path থাকে।
- **কী চলে:** JPG, PNG, WebP। প্রতিটা ছবি সর্বোচ্চ **4 MB**।
- **SVG কেন নেই:** SVG ফাইলে script লুকানো থাকতে পারে, তাই ইচ্ছা করে বাদ।
- **ছবি বদলালে বা মুছলে:** পুরনো ফাইল server থেকেও মোছে, তাই জমে জমে জায়গা নষ্ট হয় না।
- **নিরাপত্তা:** `ImageStore::delete()` শুধু `gallery/`, `news/`, `stories/`-এর ভেতরের ফাইল মোছে। database-এ ভুল path থাকলেও অন্য কিছু মুছবে না।

## Module অনুযায়ী নিয়ম

### Gallery
- **নতুন:** একসাথে সর্বোচ্চ ২০টা ছবি বাছা যায়। Category, Title, Caption, Order, Visible সব ছবিতে একসাথে বসে। বাছার পর প্রতিটা ছবির কোণে ✕ দিয়ে বাদ দেওয়া যায়।
- **Edit:** একটা ছবি। নতুন ছবি না বাছলে আগেরটাই থাকে।
- **Category:** Office / Training / Departure বা ফাঁকা। Public পাতায় শুধু যেসব category-তে ছবি আছে তার filter বোতাম দেখায়।
- **Visible switch:** বন্ধ করলে public gallery থেকে সরে যায়।

### News & Notice
- **Draft:** "Published" টিক না দিলে draft, public-এ দেখা যায় না, লিংক খুললে 404।
- **Publish:** টিক দিয়ে তারিখ ফাঁকা রাখলে **এখনই** প্রকাশ হয় (তারিখ আপনিই বসে)।
- **Schedule:** ভবিষ্যতের তারিখ-সময় দিলে ওই সময় পার হলে তবেই public-এ আসে। তালিকায় "Scheduled" দেখায়।
- **Status:** Live (চলছে) / Scheduled (সময়ের অপেক্ষায়) / Draft।
- **লেখা:** plain text, দুই paragraph-এর মাঝে একটা ফাঁকা লাইন। Public পাতায় escape হয়ে দেখায়, তাই HTML বা script চলে না।
- **Author:** নতুন খবরে যে লিখছে তার নাম `author_id`-তে বসে।
- **Cover ছবি:** ঐচ্ছিক। Edit-এ "Remove the current image" টিক দিলে ছবি মোছে।
- **Slug:** আগের module-এর মতোই। ফাঁকা রাখলে title থেকে বানায়, edit-এ বদলালে public লিংক বদলে যায়।

### Success Stories
- নাম আর গল্প (কমপক্ষে ১০ অক্ষর) বাধ্যতামূলক। Trade, Country, Photo ঐচ্ছিক।
- Photo public পাতায় গোল ছোট ছবি হয়ে দেখায়।
- **Visible switch** বন্ধ করলে public পাতা থেকে সরে যায়।
- Worker-এর অনুমতি ছাড়া গল্প বা ছবি দিও না।

## করণীয় ধাপ

### ধাপ ১: Git পরিষ্কার আছে কিনা দেখো
```bash
git status
```

### ধাপ ২: Zip extract করো
Project root-এ, "Replace" জিজ্ঞাসা করলে **Yes**। বদলাবে: `routes/web.php`, `resources/css/admin.css`, `resources/views/livewire/pages/news/index.blade.php`, `docs/PROGRESS.md`।

### ধাপ ৩: Storage লিংক (একবারই, ছবি দেখাতে **জরুরি**)
```bash
php artisan storage:link
```
আগে চালিয়ে থাকলে আবার লাগবে না। না চালালে upload হবে কিন্তু ছবি ভাঙা দেখাবে।

### ধাপ ৪: PHP upload সীমা বাড়াও
PHP-র ডিফল্ট `upload_max_filesize` মাত্র 2 MB, তাই 4 MB-এর ছবি আটকে যাবে। `php.ini`-তে:
```ini
upload_max_filesize = 5M
post_max_size = 64M
```
তারপর server (বা `php artisan serve`) আবার চালু করো। Nginx হলে `client_max_body_size 64M;` লাগে। Gallery-তে একসাথে অনেক ছবি দিলে `post_max_size` বড় লাগে। সমস্যা হলে কম কম করে দাও।

### ধাপ ৫: CSS build + test
```bash
npm run build        # বা চালু থাকলে npm run dev
php artisan optimize:clear
php artisan test
```
Test-এ নকল ছবি বানাতে PHP-র **gd extension** লাগে। `imagecreatetruecolor` error এলে gd চালু করো।

### ধাপ ৬: Browser-এ যাচাই (`php artisan serve` + `npm run dev`)
- [ ] Sidebar-এ Gallery, News & Notice, Success Stories আর "Soon" নয়
- [ ] Gallery → Add photos: ৩টা ছবি বেছে preview দেখো, একটায় ✕ দিয়ে বাদ দাও, Category বসিয়ে Save। তালিকায় ২টা আসে, `/gallery`-তে দেখায় আর filter বোতাম কাজ করে
- [ ] একটা ছবি edit করে নতুন ছবি বাছো, পুরনোটা `storage/app/public/gallery`-তে আর নেই
- [ ] ছবি delete করলে ফাইলও মোছে
- [ ] PDF বা 5 MB-এর ছবি দিলে লাল error আসে
- [ ] News: Published ছাড়া save করলে Draft, `/news`-এ নেই। পরে switch অন করলে Live হয়ে `/news`-এ আসে (cover ছবিসহ)
- [ ] News: আগামী কালের তারিখ দিলে "Scheduled", `/news/{slug}` 404
- [ ] News edit-এ "Remove the current image" টিক দিলে cover সরে যায়
- [ ] Success story বানাও (country ও photo সহ), `/success-stories`-এ গোল ছবিসহ দেখায়। Visible বন্ধ করলে সরে যায়
- [ ] Staff account দিয়ে menu-তে এই তিনটা নেই, URL-এ 403
- [ ] মোবাইল width-এ form এক কলামে, ছবি বাছা বোতাম ঠিক আছে

## সীমাবদ্ধতা
- এই sandbox-এ Laravel বসানো নেই, তাই test চালানো যায়নি। সব PHP ফাইলের syntax (`php -l`) যাচাই হয়েছে।
- ছবি ছোট করা (resize) বা compress করা হয় না, যেটা upload হয় সেটাই দেখায়। বড় ছবি দিলে পাতা ধীর হতে পারে, তাই upload-এর আগে ছোট করে নিলে ভালো।
- Country ও Training course-এর `image_path` এখনও form-এ নেই (public পাতাও ব্যবহার করে না)।
- News-এর লেখা plain text। Bold/link/ছবির জন্য editor লাগলে আলাদা step-এ করা যাবে।

## পরের step (07E): Staff Users, Roles & Permissions
