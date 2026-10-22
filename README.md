# الأفق الإخبارية — Laravel 12 + MySQL

## التشغيل
1. شغّل MySQL من XAMPP (قاعدة البيانات: `newspaper`، المستخدم root بلا كلمة مرور — عدّلها في `.env`).
2. من مجلد المشروع:
   ```
   php artisan migrate:fresh --seed
   php artisan serve
   ```
3. الموقع: http://127.0.0.1:8000 — لوحة التحكم: http://127.0.0.1:8000/admin

## الحسابات
| الدور | البريد | كلمة المرور |
|---|---|---|
| مدير النظام | admin@example.com | Admin@12345 |
| رئيس التحرير | chief@example.com | password |
| مدير التحرير | managing@example.com | password |
| رئيس قسم | section@example.com | password |
| محرر | editor1@example.com | password |
| مراسل | reporter1@example.com | password |
| مشرف التفاعل | community@example.com | password |
| مدير الإعلانات | ads@example.com | password |

## قبل الإطلاق
- غيّر كل كلمات المرور، وعيّن `APP_ENV=production` و`APP_DEBUG=false` و`APP_URL`.
- احذف `DemoSeeder` من `DatabaseSeeder` وامسح المواد التجريبية (صورها من picsum.photos).
- اضبط إعدادات البريد في `.env`، وأدخل بيانات الموقع من لوحة التحكم > إعدادات الموقع.
