# دليل رفع موقع GHOSN Relief على الاستضافة

هذا الملف يلخّص أهم ما يجب ضبطه قبل وبعد رفع الموقع على السيرفر.  
**لا ترفع ملف `.env` مع المشروع** — أنشئه على الاستضافة فقط.

---

## 1. قبل الرفع — تحقق سريع

| البند | الحالة المطلوبة |
|--------|-----------------|
| `APP_ENV=production` | إلزامي |
| `APP_DEBUG=false` | إلزامي — لا تعرض أخطاء PHP للزوار |
| `APP_KEY` | مُولَّد على السيرفر (`php artisan key:generate`) |
| `APP_URL` | رابط الموقع الكامل مع `https://` بدون `/` في النهاية |
| مجلد `public/` | **Document root** للدومين (لا تضع جذر المشروع كاملاً) |
| `npm run build` | يجب تنفيذه — مجلد `public/build/` غير موجود في git |
| `public/hot` | **لا يُرفع** — ملف تطوير Vite فقط |
| `.env` | **لا يُرفع** — موجود في `.gitignore` |
| `storage/logs/` | لا ترفع ملفات log قديمة |

---

## 2. ملف `.env` — أهم المتغيرات

### التطبيق الأساسي

```env
APP_NAME="GHOSN Relief"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=base64:...   # php artisan key:generate
LOG_LEVEL=error
```

### قاعدة البيانات

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ghosn_relief
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### الجلسات (مهم مع HTTPS)

```env
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
```

### الطابور (Queue) — مطلوب للبريد

معظم رسائل البريد (تبرعات، تواصل، متطوعين) تُرسل عبر Queue:

```env
QUEUE_CONNECTION=database
```

**يجب تشغيل worker على السيرفر بشكل دائم:**

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

(يفضّل إعداده عبر Supervisor أو cron على الاستضافة.)

---

## 3. البريد الإلكتروني

### في `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_smtp_password
MAIL_SCHEME=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="GHOSN Relief"
```

> **لا تستخدم** `MAIL_MAILER=log` على الإنتاج — الرسائل لن تصل لأحد.

### في لوحة الأدمن (بعد الدخول)

| المسار | ماذا تضبط |
|--------|-----------|
| **Settings → Email** | `From email`, `From name`, `Admin notification email`, تفعيل إشعارات الأدمن |
| **Settings → Contact** | بريد العرض + **صندوق استقبال رسائل نموذج التواصل** (`contact.inbox_email`) |
| **Settings → Payments** | بريد إيصالات التبرعات (`receipt email`) إن وُجد |

**رسائل تُرسل تلقائياً عند التفعيل:**
- تأكيد/إيصال التبرع للمتبرع
- تنبيه أدمن بتبرع جديد
- رسالة تواصل من `/contact` → صندوق الاستقبال + `/admin/messages`
- طلبات التطوّع + رسائل قبول/رفض

---

## 4. الدفع — Stripe

```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

1. في Stripe Dashboard → Webhooks → أضف endpoint:  
   `https://yourdomain.com/webhooks/stripe`
2. في الأدمن: **Settings → Payments** → فعّل Stripe.

---

## 5. الدفع — PayPal

```env
PAYPAL_MODE=live
PAYPAL_CLIENT_ID=...
PAYPAL_CLIENT_SECRET=...
PAYPAL_WEBHOOK_ID=...    # مهم — بدونه لا تُحدَّث حالة الدفع تلقائياً
```

1. Webhook URL: `https://yourdomain.com/webhooks/paypal`
2. في الأدmin: **Settings → Payments** → `PayPal mode = live` + تفعيل PayPal.

---

## 6. PHP على Hostinger (مهم جداً)

المشروع يتطلب **PHP >= 8.3**. على Hostinger غالباً:

| المكان | ماذا تضبط |
|--------|-----------|
| hPanel → PHP Configuration | اختر **8.3** (للموقع في المتصفح) |
| SSH / CLI | أمر `php` الافتراضي قد يكون **8.2** — استخدم مسار 8.3 صراحة |

تحقق من CLI:

```bash
php -v
/opt/alt/php83/usr/bin/php -v
ls /opt/alt/php*/usr/bin/php
```

> استخدم دائماً `/opt/alt/php83/usr/bin/php` مع `artisan` و `composer` من SSH حتى لا يظهر خطأ:  
> `Your Composer dependencies require a PHP version ">= 8.3.0"`.

---

## 6.b أوامر ما بعد الرفع (مرة واحدة)

من جذر المشروع على السيرفر (مثال: مجلد `ghosnps` — تأكد بـ `pwd` ووجود ملف `artisan`):

```bash
/opt/alt/php83/usr/bin/php /usr/bin/composer install --no-dev --optimize-autoloader
# أو إن كان composer.phar محلياً:
# /opt/alt/php83/usr/bin/php composer.phar install --no-dev --optimize-autoloader

npm ci && npm run build

/opt/alt/php83/usr/bin/php artisan migrate --force
/opt/alt/php83/usr/bin/php artisan storage:link
/opt/alt/php83/usr/bin/php artisan config:cache
/opt/alt/php83/usr/bin/php artisan route:cache
/opt/alt/php83/usr/bin/php artisan view:cache
```

**صلاحيات الكتابة** (حسب المستخدم على الاستضافة):

```bash
chmod -R ug+rwx storage bootstrap/cache
```

---

## 7. حساب الأدمن — مهم جداً

- **لا تشغّل** `php artisan db:seed` على الإنتاج — الـ seeder المحلي ينشئ `admin@ghosn.test` / `password`.
- أنشئ مستخدم أدمن يدوياً أو عبر tinker بعد `RolePermissionSeeder` فقط:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

ثم أنشئ مستخدماً من **Admin → Users** ببريد حقيقي وكلمة مرور قوية، وعيّن دور **Super Admin**.

> على الإنتاج: أي مستخدم بدون `role_id` **لا يستطيع** الدخول للأدمن (تم إغلاق ثغرة الترقية التلقائية).

---

## 8. إعدادات المحتوى من الأدمن

| الصفحة / القسم | المسار في الأدمن |
|----------------|------------------|
| القائمة والفوتر | Settings → Navigation / Footer |
| صفحة التواصل | Settings → Contact |
| الصفحات القانونية | Settings → Legal pages |
| صفحة فريقنا | Settings → Our Team page |
| وسائل التواصل | Settings → Social |
| الصفحة الرئيسية | Pages Builder |
| المقالات والحملات | Admin → Posts / Campaigns |
| **وضع الصيانة** | Settings → Maintenance |

---

## 8.b وضع الصيانة (من الأدمن)

- المسار: **Settings → Maintenance** (`/admin/settings/maintenance`)
- فعّل **Enable maintenance mode** لإظهار صفحة صيانة للزوار
- **لوحة الأدمن** و **webhooks** و **`/up`** تبقى تعمل
- عدّل العنوان والرسالة بالعربية والإنجليزية قبل التفعيل

---

## 9. ما لا ترفعه على الاستضافة

| الملف / المجلد | السبب |
|----------------|--------|
| `.env` | أسرار |
| `node_modules/` | يُبنى محلياً أو على السيرفر للـ build فقط |
| `vendor/` | `composer install` على السيرفر |
| `tests/`, `phpunit.xml` | تطوير فقط |
| `.phpunit.result.cache` | تطوير |
| `public/hot` | Vite dev |
| `storage/logs/*.log` | سجلات محلية |
| `GHOSN Relief Team Landing Page/` | ملفات تصميم DC — ليست جزءاً من التشغيل |

---

## 10. أمان — ما تم تطبيقه في الكود

- CSRF على نماذج الويب
- Rate limit: نماذج عامة (10/دقيقة)، تسجيل دخول الأدmin (5/دقيقة)
- صلاحيات RBAC على كل مسارات `/admin`
- التحقق من توقيع Stripe/PayPal webhooks
- رفع ملفات: MIME + امتداد + حجم محدود (أدمن فقط)
- `robots.txt`: منع فهرسة `/admin`
- HTTPS إجباري لتوليد الروابط في `production`
- كوكيز الجلسة آمنة افتراضياً في `production`

---

## 11. Cron / Supervisor (مُوصى به)

**Queue worker** (Supervisor) — استخدم مسار PHP 8.3 ومسار المشروع الفعلي:

```ini
[program:ghosn-queue]
command=/opt/alt/php83/usr/bin/php /home/USER/ghosnps/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=USER
```

**Scheduler** (إن أضفت مهام مجدولة لاحقاً):

```cron
* * * * * cd /home/USER/ghosnps && /opt/alt/php83/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

بدّل `USER` و`ghosnps` حسب حسابك ومجلد المشروع (`pwd` على السيرفر).

---

## 12. قائمة تحقق نهائية قبل الإطلاق

- [ ] `APP_DEBUG=false` و `APP_URL` صحيح
- [ ] SSL (HTTPS) يعمل على الدومين
- [ ] PHP في hPanel = **8.3** و CLI عبر `/opt/alt/php83/usr/bin/php`
- [ ] **SEO / مشاركة الروابط:** من `/admin/settings/seo` — عنوان، نبذة، وصورة (1200×630). يجب أن يكون `APP_URL` بـ `https://` حتى تظهر الصورة على واتساب (روابط مطلقة)
- [ ] قاعدة البيانات مهاجرة
- [ ] `storage:link` + صلاحيات `storage/` و `bootstrap/cache/`
- [ ] `npm run build` — الموقع يحمّل CSS/JS من `public/build/`
- [ ] البريد SMTP مُختبر (أرسل رسالة من `/contact`)
- [ ] Queue worker يعمل
- [ ] إيميلات الأدمن مضبوطة في Settings
- [ ] Stripe/PayPal live + webhooks (إن مُفعّل)
- [ ] حساب أدمن حقيقي بكلمة مرور قوية — **بدون** seed محلي
- [ ] جرّب: تبرع، تواصل، تطوّع، دخول أدمن

---

## 13. تحديث الموقع (محلي → GitHub → السيرفر)

### أ) من جهازك المحلي (Windows / PowerShell)

```powershell
cd c:\laragon\www\ghosn-relief

git status
git pull origin main

git add .
# تجنّب رفع ملفات غير ضرورية مثل public/build.zip إن وُجدت:
# git reset HEAD public/build.zip

git commit -m "Describe your change"
git push origin main
```

### ب) على السيرفر (SSH — من جذر المشروع)

تأكد أولاً أنك داخل المجلد الصحيح (يجب أن ترى ملف `artisan`):

```bash
pwd
ls artisan
```

ثم حدّث الكود والاعتماديات والكاش:

```bash
git pull origin main

/opt/alt/php83/usr/bin/php /usr/bin/composer install --no-dev --optimize-autoloader

/opt/alt/php83/usr/bin/php artisan migrate --force

/opt/alt/php83/usr/bin/php artisan config:clear
/opt/alt/php83/usr/bin/php artisan cache:clear
/opt/alt/php83/usr/bin/php artisan view:clear
/opt/alt/php83/usr/bin/php artisan route:clear

/opt/alt/php83/usr/bin/php artisan config:cache
/opt/alt/php83/usr/bin/php artisan route:cache
/opt/alt/php83/usr/bin/php artisan view:cache

/opt/alt/php83/usr/bin/php artisan queue:restart
```

إذا تغيّر CSS/JS (Vite):

```bash
npm ci
npm run build
```

### ج) مسح وإعادة بناء الكاش فقط (بدون pull)

```bash
/opt/alt/php83/usr/bin/php artisan config:clear
/opt/alt/php83/usr/bin/php artisan cache:clear
/opt/alt/php83/usr/bin/php artisan view:clear
/opt/alt/php83/usr/bin/php artisan route:clear
/opt/alt/php83/usr/bin/php artisan config:cache
/opt/alt/php83/usr/bin/php artisan route:cache
/opt/alt/php83/usr/bin/php artisan view:cache
```

### د) ملاحظات شائعة

| المشكلة | الحل |
|---------|------|
| `Could not open input file: /opt/ghosn-relief/artisan` | مسار المشروع غلط — ادخل مجلد المشروع (`ghosnps` مثلاً) واستخدم `php artisan` أو المسار الكامل لـ `artisan` هناك |
| `PHP version ">= 8.3.0". You are running 8.2.x` | لا تستخدم `php` العادي — استخدم `/opt/alt/php83/usr/bin/php` |
| خطأ Blade قديم بعد الإصلاح | `git pull` ثم `view:clear` و`view:cache` |
| تعديل محلي ما ظهر على الموقع | تأكد أنك عملت `commit` + `push` ثم `git pull` على السيرفر |

```bash
# فحص أمان الحزم
/opt/alt/php83/usr/bin/php /usr/bin/composer audit
```

---

*آخر مراجعة: أوامر التحديث على Hostinger + PHP 8.3 CLI — GHOSN Relief Team*
