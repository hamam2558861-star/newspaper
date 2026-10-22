<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BaseSeeder::class,   // أدوار، مدير النظام، أقسام، صفحات، إعدادات (ضروري للتشغيل)
            DemoSeeder::class,   // مواد وبيانات تجريبية (احذفه من هنا قبل الإطلاق الفعلي)
        ]);
    }
}
