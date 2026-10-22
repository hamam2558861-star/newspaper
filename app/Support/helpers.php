<?php

use App\Models\Ad;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('upload_url')) {
    /** رابط ملف مرفوع (أو رابط خارجي كما هو). */
    function upload_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : asset('uploads/' . $path);
    }
}

if (! function_exists('store_upload')) {
    /** يحفظ الملف داخل public/uploads/{dir} ويعيد المسار النسبي. */
    function store_upload(UploadedFile $file, string $dir): string
    {
        $name = Str::random(20) . '.' . strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $file->move(public_path("uploads/$dir"), $name);

        return "$dir/$name";
    }
}

if (! function_exists('delete_upload')) {
    function delete_upload(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            File::delete(public_path('uploads/' . $path));
        }
    }
}

if (! function_exists('ad_slot')) {
    /** يعيد إعلاناً فعالاً للموضع المطلوب (أو null). */
    function ad_slot(string $position): ?Ad
    {
        return Ad::running()->where('position', $position)->inRandomOrder()->first();
    }
}

if (! function_exists('ar_date')) {
    function ar_date($date, string $format = 'j F Y'): string
    {
        return $date ? $date->copy()->locale('ar')->translatedFormat($format) : '';
    }
}

if (! function_exists('sanitize_html')) {
    /** تنظيف مبسّط لمحتوى المحرر: إزالة السكربتات ومعالجات الأحداث. */
    function sanitize_html(?string $html): string
    {
        $html = (string) $html;
        $html = preg_replace('#<(script|style|iframe|object|embed|form)[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|iframe|object|embed|form)[^>]*/?>#is', '', $html);
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1=$2#$2', $html);

        return $html;
    }
}
