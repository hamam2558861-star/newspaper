<?php

/*
| تعريف الصلاحيات والأدوار الوظيفية لغرفة الأخبار.
| يمكن لمدير النظام تعديل صلاحيات كل دور من لوحة التحكم (الإدارة > الأدوار).
*/
return [

    'permissions' => [
        'المحتوى' => [
            'articles.create'    => 'كتابة الأخبار والمقالات',
            'articles.edit_own'  => 'تعديل المواد الخاصة (مسودة/مرفوضة/قيد المراجعة)',
            'articles.edit_all'  => 'تعديل جميع المواد',
            'articles.publish'   => 'مراجعة المواد واعتمادها ونشرها وجدولتها',
            'articles.delete'    => 'حذف المواد',
            'taxonomy.manage'    => 'إدارة الأقسام والوسوم',
            'pages.manage'       => 'إدارة الصفحات الثابتة',
        ],
        'التفاعل' => [
            'comments.moderate'  => 'إدارة التعليقات',
            'messages.view'      => 'رسائل التواصل',
            'subscribers.manage' => 'المشتركون في النشرة البريدية',
        ],
        'الإعلانات' => [
            'ads.manage'         => 'إدارة الإعلانات',
        ],
        'النظام' => [
            'users.manage'       => 'إدارة المستخدمين',
            'roles.manage'       => 'إدارة الأدوار والصلاحيات',
            'settings.manage'    => 'إعدادات الموقع',
            'logs.view'          => 'سجل النشاط',
        ],
    ],

    // الأدوار الافتراضية (تُزرع عند تشغيل الـ Seeder)
    'roles' => [
        'super_admin' => [
            'title' => 'مدير النظام',
            'description' => 'صلاحيات كاملة على الموقع ولوحة التحكم.',
            'permissions' => ['*'],
        ],
        'editor_in_chief' => [
            'title' => 'رئيس التحرير',
            'description' => 'المسؤول الأول عن المحتوى: يعتمد وينشر ويدير الأقسام والصفحات.',
            'permissions' => ['articles.create', 'articles.edit_own', 'articles.edit_all', 'articles.publish', 'articles.delete', 'taxonomy.manage', 'pages.manage', 'comments.moderate', 'messages.view', 'subscribers.manage', 'logs.view'],
        ],
        'managing_editor' => [
            'title' => 'مدير التحرير',
            'description' => 'يشرف على سير العمل التحريري ويراجع المواد وينشرها.',
            'permissions' => ['articles.create', 'articles.edit_own', 'articles.edit_all', 'articles.publish', 'taxonomy.manage', 'comments.moderate'],
        ],
        'section_editor' => [
            'title' => 'رئيس قسم',
            'description' => 'يراجع مواد قسمه ويعتمدها للنشر.',
            'permissions' => ['articles.create', 'articles.edit_own', 'articles.edit_all', 'articles.publish'],
        ],
        'editor' => [
            'title' => 'محرر',
            'description' => 'يكتب ويحرر المواد ويرسلها للمراجعة.',
            'permissions' => ['articles.create', 'articles.edit_own'],
        ],
        'reporter' => [
            'title' => 'مراسل',
            'description' => 'يرسل الأخبار الميدانية ومسودات المواد للمراجعة.',
            'permissions' => ['articles.create', 'articles.edit_own'],
        ],
        'community_manager' => [
            'title' => 'مشرف التفاعل',
            'description' => 'يدير التعليقات ورسائل القراء والنشرة البريدية.',
            'permissions' => ['comments.moderate', 'messages.view', 'subscribers.manage'],
        ],
        'ad_manager' => [
            'title' => 'مدير الإعلانات',
            'description' => 'يدير الحملات والمساحات الإعلانية.',
            'permissions' => ['ads.manage'],
        ],
    ],

    'article_types' => [
        'news'    => 'خبر',
        'report'  => 'تقرير',
        'opinion' => 'رأي ومقال',
        'video'   => 'فيديو',
    ],

    'article_statuses' => [
        'draft'     => 'مسودة',
        'pending'   => 'بانتظار المراجعة',
        'published' => 'منشور',
        'rejected'  => 'مرفوض',
        'archived'  => 'مؤرشف',
    ],

    'ad_positions' => [
        'header'         => 'أعلى الصفحة (728×90)',
        'sidebar_top'    => 'الشريط الجانبي - أعلى (300×250)',
        'sidebar_bottom' => 'الشريط الجانبي - أسفل (300×600)',
        'article_inline' => 'داخل المقال (728×90)',
        'footer'         => 'قبل التذييل (970×90)',
    ],
];
