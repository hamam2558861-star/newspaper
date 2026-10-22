<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public const FIELDS = [
        'site_name', 'site_tagline', 'site_description', 'contact_email', 'contact_phone', 'contact_address',
        'facebook', 'x_twitter', 'instagram', 'youtube', 'telegram',
        'footer_about', 'copyright', 'head_code', 'comments_enabled', 'comments_auto_approve', 'articles_per_page',
    ];

    public function edit()
    {
        return view('admin.settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:80'],
            'contact_email' => ['nullable', 'email'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:512'],
            'articles_per_page' => ['nullable', 'integer', 'min:6', 'max:60'],
        ], [], ['site_name' => 'اسم الموقع']);

        foreach (self::FIELDS as $field) {
            if (in_array($field, ['comments_enabled', 'comments_auto_approve'])) {
                Setting::put($field, $request->boolean($field) ? '1' : '0');
            } else {
                Setting::put($field, $request->input($field));
            }
        }
        foreach (['logo', 'favicon'] as $file) {
            if ($request->hasFile($file)) {
                delete_upload(setting($file));
                Setting::put($file, store_upload($request->file($file), 'site'));
            }
        }
        ActivityLog::record('update', 'تحديث إعدادات الموقع');

        return back()->with('success', 'تم حفظ الإعدادات.');
    }
}
