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

## 6. أوامر ما بعد الرفع (مرة واحدة)

نفّذ من جذر المشروع على السيرفر:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**صلاحيات الكتابة** (مستخدم الويب = www-data أو nginx):

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
| **وضع الصيانة** | Settings → Maintenance |

---

## 8.b وضع الصيانة (من الأدمن)

- المسار: **Settings → Maintenance** (`/admin/settings/maintenance`)
- فعّل **Enable maintenance mode** لإظهار صفحة صيانة للزوار
- **لوحة الأدmin** و **webhooks** و **`/up`** تبقى تعمل
- عدّل العنوان والرسالة بالعربية والإنجليزية قبل التفعيل

---

## 9. ما لا ترفع على الاستضافة
| وسائل التواصل | Settings → Social |
| الصفحة الرئيسية | Pages Builder |
| المقالات والحملات | Admin → Posts / Campaigns |

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

**Queue worker** (Supervisor):

```ini
[program:ghosn-queue]
command=php /path/to/ghosn-relief/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
```

**Scheduler** (إن أضفت مهام مجدولة لاحقاً):

```cron
* * * * * cd /path/to/ghosn-relief && php artisan schedule:run >> /dev/null 2>&1
```

---

## 12. قائمة تحقق نهائية قبل الإطلاق

- [ ] `APP_DEBUG=false` و `APP_URL` صحيح
- [ ] SSL (HTTPS) يعمل على الدومين
- [ ] **SEO / مشاركة الروابط:** من `/admin/settings/seo` — عنوان، نبذة، وصورة (1200×630). يجب أن يكون `APP_URL` بـ `https://` حتى تظهر الصورة على واتساب (روابط مطلقة)
- [ ] قاعدة البيانات مهاجرة
- [ ] `storage:link` + صلاحيات `storage/` و `bootstrap/cache/`
- [ ] `npm run build` — الموقع يحمّل CSS/JS من `public/build/`
- [ ] البريد SMTP مُختبر (أرسل رسالة من `/contact`)
- [ ] Queue worker يعمل
- [ ] إيميلات الأدmin مضبوطة في Settings
- [ ] Stripe/PayPal live + webhooks (إن مُفعّل)
- [ ] حساب أدمن حقيقي بكلمة مرور قوية — **بدون** seed محلي
- [ ] جرّب: تبرع، تواصل، تطوّع، دخول أدمن

---

## 13. دعم وصيانة

```bash
# تحديث الكود بعد git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

```bash
# فحص أمان الحزم
composer audit
```

---

*آخر مراجعة: تجهيز ما قبل الإنتاج — GHOSN Relief Team*
