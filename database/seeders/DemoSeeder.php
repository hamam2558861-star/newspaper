<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * بيانات تجريبية بالكامل (عناوين ونصوص افتراضية) لمعاينة التصميم فقط.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->users();
        $cats = Category::pluck('id', 'slug');

        $paras = [
            'أكدت مصادر مطلعة أن الاجتماعات التي عُقدت خلال الأيام الماضية أفضت إلى جملة من التفاهمات الأولية، على أن تُستكمل المشاورات في الأسابيع المقبلة بمشاركة الأطراف المعنية كافة.',
            'ويرى مراقبون أن هذه الخطوة تمثّل تحولاً مهماً في المشهد الحالي، لما لها من انعكاسات مباشرة على الحياة اليومية للمواطنين وعلى مسار التطورات المتوقعة خلال الفترة القادمة.',
            'وفي تصريح خاص، شدّد أحد المسؤولين على أن العمل مستمر وفق جدول زمني واضح، مشيراً إلى أن النتائج الأولية تبعث على التفاؤل، وأن التقييم النهائي سيُعلن فور اكتمال المراحل المقررة.',
            'من جهتهم، أشار خبراء في المجال إلى أن نجاح التجربة يعتمد على عوامل عدة، من أبرزها الشفافية في التنفيذ، والتنسيق بين الجهات المختلفة، وقدرة المؤسسات على التكيّف مع المتغيرات المتسارعة.',
            'ويُنتظر أن تصدر التفاصيل الكاملة خلال الأيام القليلة المقبلة، فيما تواصل «الأفق» متابعة التطورات أولاً بأول وتقديم المزيد من التفاصيل فور ورودها.',
        ];
        $body = function (int $seed) use ($paras) {
            $p = collect($paras)->map(fn ($t) => "<p>$t</p>");
            $sub = '<h2>أبرز التفاصيل</h2>';
            $quote = '<blockquote>«الأرقام وحدها لا تكفي، فالمهم هو الأثر الذي يلمسه الناس في حياتهم اليومية».</blockquote>';

            return $p[0] . $p[1] . $sub . $p[2] . $quote . $p[3] . $p[4];
        };

        // [قسم, نوع, عنوان, مقدمة فوقية, كاتب]
        $items = [
            ['politics', 'news', 'اجتماع رفيع المستوى يبحث ملفات التعاون الإقليمي والتنسيق المشترك', 'سياسة', 'editor1'],
            ['politics', 'news', 'لجنة برلمانية تناقش مشروع قانون جديد لتنظيم العمل البلدي', 'تشريعات', 'reporter1'],
            ['politics', 'report', 'تقرير: كيف تغيّرت أولويات الناخبين في السنوات الأخيرة؟', 'تقرير خاص', 'editor1'],
            ['politics', 'news', 'قمة مرتقبة تجمع قادة المنطقة لبحث الأمن والتنمية', 'عاجل', 'reporter1'],
            ['world', 'news', 'مباحثات دولية جديدة حول ملفات المناخ والطاقة النظيفة', 'العالم', 'reporter2'],
            ['world', 'report', 'أبرز محطات العام في العلاقات الدولية: قراءة في المشهد', 'تحليل', 'editor2'],
            ['world', 'news', 'منظمة أممية تدعو إلى تعزيز الدعم الإنساني للمناطق المتضررة', 'إنساني', 'reporter2'],
            ['economy', 'news', 'الأسواق المالية تغلق على ارتفاع وسط تفاؤل المستثمرين', 'أسواق', 'editor2'],
            ['economy', 'report', 'قطاع الطاقة المتجددة يستقطب استثمارات قياسية هذا العام', 'تقرير', 'editor2'],
            ['economy', 'news', 'ارتفاع مؤشر الثقة بالاقتصاد المحلي للربع الثالث على التوالي', 'اقتصاد', 'reporter1'],
            ['economy', 'news', 'مبادرة جديدة لدعم المشاريع الناشئة وتمويل رواد الأعمال', 'ريادة', 'editor1'],
            ['sports', 'news', 'فوز مثير يضع الفريق في صدارة جدول الترتيب بعد جولة حاسمة', 'كرة القدم', 'reporter3'],
            ['sports', 'news', 'المنتخب الوطني يبدأ معسكره التدريبي استعداداً للاستحقاقات القادمة', 'منتخبات', 'reporter3'],
            ['sports', 'video', 'أجمل أهداف الجولة: لقطات لا تُنسى من الملاعب', 'فيديو', 'reporter3'],
            ['sports', 'report', 'من المدرجات إلى الشاشات: كيف تغيّر مشهد التشجيع الرياضي؟', 'تقرير', 'reporter3'],
            ['tech', 'news', 'شركات التقنية تتسابق لإطلاق جيل جديد من المساعدات الذكية', 'ذكاء اصطناعي', 'editor2'],
            ['tech', 'report', 'الأمن السيبراني: 7 خطوات بسيطة لحماية حساباتك الشخصية', 'دليل', 'editor2'],
            ['tech', 'video', 'جولة داخل مختبر الروبوتات: هكذا تُصنع آلات المستقبل', 'فيديو', 'editor2'],
            ['tech', 'news', 'إطلاق منصة وطنية جديدة لتعليم البرمجة لطلبة المدارس', 'تعليم', 'reporter1'],
            ['culture', 'news', 'معرض الكتاب ينطلق بمشاركة مئات دور النشر من الوطن العربي', 'كتب', 'editor1'],
            ['culture', 'report', 'السينما العربية في عام: أفلام صنعت الفارق وجوائز لافتة', 'سينما', 'editor1'],
            ['culture', 'video', 'حوار خاص مع روائي عن رحلة الكتابة والنشر', 'فيديو', 'editor1'],
            ['health', 'news', 'دراسة حديثة: المشي اليومي يخفّض مخاطر أمراض القلب', 'صحة القلب', 'reporter2'],
            ['health', 'report', 'دليلك الغذائي لموسم الشتاء: ما الذي يعزز المناعة فعلاً؟', 'تغذية', 'reporter2'],
            ['health', 'news', 'وزارة الصحة تطلق حملة وطنية للكشف المبكر عن الأمراض المزمنة', 'حملة', 'reporter2'],
            ['science', 'news', 'علماء يرصدون ظاهرة فلكية نادرة تُشاهد بالعين المجردة', 'فلك', 'editor2'],
            ['science', 'report', 'التغير المناخي: ماذا تقول أحدث البيانات عن العقد المقبل؟', 'بيئة', 'editor2'],
            ['science', 'news', 'مشروع لزراعة مليون شجرة يدخل مرحلته الثانية بنجاح', 'بيئة', 'reporter1'],
            ['politics', 'opinion', 'لماذا تحتاج المنطقة إلى لغة سياسية جديدة؟', 'رأي', 'writer1'],
            ['economy', 'opinion', 'الاقتصاد الرقمي: فرصة أم عبء إضافي على الشباب؟', 'رأي', 'writer2'],
            ['culture', 'opinion', 'القراءة في زمن الشاشات.. معركة لم تُحسم بعد', 'رأي', 'writer1'],
            ['sports', 'opinion', 'الاحتراف الرياضي: ما بين الطموح وواقع البنية التحتية', 'رأي', 'writer2'],
        ];

        $tagPool = ['عاجل', 'تحليل', 'تقرير خاص', 'الاقتصاد الرقمي', 'الذكاء الاصطناعي', 'المناخ', 'كرة القدم', 'الصحة العامة', 'الطاقة', 'الشباب', 'التعليم', 'الثقافة'];

        foreach ($items as $i => [$cat, $type, $title, $kicker, $who]) {
            $article = Article::create([
                'category_id' => $cats[$cat],
                'user_id' => $users[$who]->id,
                'reviewed_by' => $users['chief']->id,
                'kicker' => $kicker,
                'title' => $title,
                'slug' => Article::makeSlug($title),
                'excerpt' => 'تفاصيل ومعلومات إضافية حول الموضوع في هذا التقرير الذي يرصد أبرز المستجدات ويقدّم قراءة مهنية للحدث وتداعياته المتوقعة.',
                'body' => $body($i),
                'image' => "https://picsum.photos/seed/afuq{$i}/1200/675",
                'image_caption' => 'صورة توضيحية — لقطة من الأرشيف',
                'video_url' => $type === 'video' ? 'https://www.youtube.com/watch?v=aqz-KE-bpKQ' : null,
                'type' => $type,
                'status' => 'published',
                'dateline' => ['عمّان', 'القدس', 'القاهرة', 'الرياض', 'بيروت', 'دبي'][$i % 6],
                'is_featured' => $i < 7 && $type !== 'opinion',
                'is_breaking' => in_array($i, [0, 3, 7]),
                'views' => random_int(120, 9800),
                'published_at' => now()->subMinutes($i * 95 + random_int(5, 60)),
            ]);
            $tagIds = collect($tagPool)->random(3)->map(fn ($n) => Tag::firstOrCreate(['slug' => Article::makeSlug($n)], ['name' => $n])->id);
            $article->tags()->sync($tagIds);

            if ($i % 3 === 0) {
                Comment::create(['article_id' => $article->id, 'name' => 'قارئ كريم', 'email' => 'reader@example.com', 'body' => 'مادة مفيدة ومكتوبة بأسلوب واضح، شكراً لكم على هذه التغطية.', 'status' => 'approved']);
            }
        }

        // مواد بحالات مختلفة لتجربة سير العمل التحريري
        Article::create([
            'category_id' => $cats['politics'], 'user_id' => $users['reporter1']->id, 'title' => 'مسودة خبر ميداني بانتظار الإرسال للمراجعة',
            'slug' => 'draft-demo', 'body' => '<p>هذه مسودة تجريبية.</p>', 'type' => 'news', 'status' => 'draft', 'excerpt' => 'مسودة تجريبية.',
        ]);
        foreach (['اقتصاد' => 'economy', 'رياضة' => 'sports'] as $label => $slug) {
            Article::create([
                'category_id' => $cats[$slug], 'user_id' => $users['reporter2']->id, 'title' => "خبر $label بانتظار موافقة رئيس القسم",
                'slug' => Article::makeSlug("خبر $label بانتظار موافقة"), 'body' => '<p>نص تجريبي بانتظار المراجعة والاعتماد.</p>', 'type' => 'news', 'status' => 'pending', 'excerpt' => 'نص تجريبي بانتظار المراجعة.',
            ]);
        }
        Comment::create(['article_id' => Article::first()->id, 'name' => 'زائر', 'email' => 'visitor@example.com', 'body' => 'تعليق بانتظار الموافقة من المشرف.', 'status' => 'pending']);

        // إعلانات تجريبية
        Ad::firstOrCreate(['title' => 'إعلان تجريبي - الشريط الجانبي'], ['position' => 'sidebar_top', 'code' => '<div class="ad-demo" style="width:300px;height:250px;max-width:100%"><span dir=\"ltr\">300×250</span></div>', 'is_active' => true]);
        Ad::firstOrCreate(['title' => 'إعلان تجريبي - أعلى الصفحة'], ['position' => 'header', 'code' => '<div class="ad-demo" style="width:728px;height:90px;max-width:100%"><span dir=\"ltr\">728×90</span></div>', 'is_active' => true]);
    }

    private function users(): array
    {
        $defs = [
            'chief'    => ['رئيس التحرير', 'editor_in_chief', 'رئيس التحرير', 'chief@example.com'],
            'managing' => ['مدير التحرير', 'managing_editor', 'مدير التحرير', 'managing@example.com'],
            'section'  => ['رئيس قسم', 'section_editor', 'رئيس قسم السياسة', 'section@example.com'],
            'editor1'  => ['ليلى الحسن', 'editor', 'محررة أولى', 'editor1@example.com'],
            'editor2'  => ['عمر الخطيب', 'editor', 'محرر الشؤون الاقتصادية والتقنية', 'editor2@example.com'],
            'reporter1' => ['سامي النجار', 'reporter', 'مراسل ميداني', 'reporter1@example.com'],
            'reporter2' => ['هدى المصري', 'reporter', 'مراسلة', 'reporter2@example.com'],
            'reporter3' => ['يوسف الراشد', 'reporter', 'مراسل رياضي', 'reporter3@example.com'],
            'writer1'  => ['د. منى العلي', 'editor', 'كاتبة وأكاديمية', 'writer1@example.com'],
            'writer2'  => ['خالد المنصور', 'editor', 'كاتب ومحلل اقتصادي', 'writer2@example.com'],
            'community' => ['مشرف التفاعل', 'community_manager', 'مشرف التفاعل', 'community@example.com'],
            'ads'      => ['مدير الإعلانات', 'ad_manager', 'مدير الإعلانات', 'ads@example.com'],
        ];

        $out = [];
        foreach ($defs as $key => [$name, $role, $job, $email]) {
            $out[$key] = User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => 'password',
                'role_id' => Role::where('name', $role)->value('id'),
                'job_title' => $job,
                'bio' => 'نبذة تعريفية مختصرة عن الكاتب وخبراته في العمل الصحفي.',
                'is_active' => true,
            ]);
        }

        return $out;
    }
}

