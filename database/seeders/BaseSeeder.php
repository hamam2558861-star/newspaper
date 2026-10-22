<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function run(): void
    {
        // الأدوار
        foreach (config('newsroom.roles') as $name => $def) {
            Role::updateOrCreate(['name' => $name], [
                'title' => $def['title'],
                'description' => $def['description'],
                'permissions' => $def['permissions'],
                'is_system' => true,
            ]);
        }

        // مدير النظام
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'مدير النظام',
            'password' => 'Admin@12345',
            'role_id' => Role::where('name', 'super_admin')->value('id'),
            'job_title' => 'مدير النظام',
            'is_active' => true,
        ]);

        // الأقسام
        $cats = [
            ['politics', 'سياسة', '#b91c1c'],
            ['world', 'العالم', '#1d4ed8'],
            ['economy', 'اقتصاد', '#047857'],
            ['sports', 'رياضة', '#ea580c'],
            ['tech', 'تكنولوجيا', '#7c3aed'],
            ['culture', 'ثقافة وفنون', '#be185d'],
            ['health', 'صحة', '#0e7490'],
            ['science', 'علوم وبيئة', '#4d7c0f'],
        ];
        foreach ($cats as $i => [$slug, $name, $color]) {
            Category::firstOrCreate(['slug' => $slug], ['name' => $name, 'color' => $color, 'sort_order' => $i + 1]);
        }

        // الصفحات الثابتة
        $pages = [
            ['about', 'من نحن', '<p>نحن مؤسسة إعلامية مستقلة تقدّم تغطية إخبارية شاملة ومتوازنة على مدار الساعة، وتلتزم بالدقة والمهنية وخدمة القارئ.</p><h2>رسالتنا</h2><p>نقل الحقيقة كما هي، وإتاحة المعلومة الموثوقة لكل قارئ بلغة واضحة وأسلوب مهني، مع احترام تنوّع الآراء وحق الجمهور في المعرفة.</p><h2>قيمنا</h2><ul><li>الدقة والتحقق قبل النشر</li><li>الاستقلالية والنزاهة</li><li>الشفافية في المصادر</li><li>احترام الخصوصية والكرامة الإنسانية</li></ul>'],
            ['editorial-policy', 'ميثاق الشرف المهني', '<p>تلتزم أسرة التحرير بالمعايير المهنية التالية في جميع المواد المنشورة:</p><ol><li>التحقق من المعلومات من أكثر من مصدر كلما أمكن.</li><li>الفصل الواضح بين الخبر والرأي.</li><li>تصحيح الأخطاء بشفافية وسرعة.</li><li>عدم نشر ما يحرّض على الكراهية أو العنف.</li></ol>'],
            ['privacy', 'سياسة الخصوصية', '<p>نحترم خصوصية زوارنا ونجمع الحد الأدنى من البيانات اللازمة لتشغيل الموقع وتحسين الخدمة، ولا نشارك بياناتك الشخصية مع أي جهة دون موافقتك إلا بما يقتضيه القانون.</p><h2>البيانات التي نجمعها</h2><p>البريد الإلكتروني عند الاشتراك في النشرة، والاسم والبريد عند إرسال تعليق أو رسالة، وبيانات تصفح مجهولة لأغراض الإحصاء.</p>'],
            ['terms', 'شروط الاستخدام', '<p>باستخدامك لهذا الموقع فإنك توافق على الشروط التالية: جميع المواد محمية بحقوق النشر، ولا يجوز إعادة نشرها دون إذن مكتوب، مع جواز الاقتباس المحدود مع الإشارة إلى المصدر ورابط المادة.</p><h2>التعليقات</h2><p>التعليقات تعبّر عن أصحابها فقط، ويحق للإدارة حذف أي تعليق مخالف للآداب العامة أو يتضمن إساءة أو تحريضاً.</p>'],
            ['advertise', 'أعلن معنا', '<p>للإعلان على موقعنا والوصول إلى جمهور واسع من القرّاء، يرجى التواصل مع قسم الإعلانات عبر صفحة «اتصل بنا» مع ذكر نوع الحملة والفترة المطلوبة.</p>'],
        ];
        foreach ($pages as [$slug, $title, $body]) {
            Page::firstOrCreate(['slug' => $slug], ['title' => $title, 'body' => $body]);
        }

        // الإعدادات الافتراضية
        $defaults = [
            'site_name' => 'الأفق الإخبارية',
            'site_tagline' => 'الخبر الموثوق.. في وقته',
            'site_description' => 'صحيفة إخبارية مستقلة تقدّم تغطية شاملة لأهم الأحداث في السياسة والاقتصاد والرياضة والثقافة والتكنولوجيا.',
            'contact_email' => 'info@example.com',
            'contact_phone' => '+962 6 000 0000',
            'contact_address' => 'عمّان، المملكة الأردنية الهاشمية',
            'footer_about' => 'منصة إخبارية رقمية تلتزم بالمهنية والدقة، وتنقل لكم الحدث أولاً بأول من مختلف أنحاء العالم.',
            'copyright' => 'جميع الحقوق محفوظة',
            'comments_enabled' => '1',
            'comments_auto_approve' => '0',
            'articles_per_page' => '12',
        ];
        foreach ($defaults as $k => $v) {
            if (Setting::find($k) === null) {
                Setting::put($k, $v);
            }
        }
    }
}
