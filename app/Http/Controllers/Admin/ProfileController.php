<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'job_title' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:600'],
            'phone' => ['nullable', 'string', 'max:30'],
            'twitter' => ['nullable', 'string', 'max:60'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
        ], [], [
            'name' => 'الاسم', 'email' => 'البريد', 'password' => 'كلمة المرور', 'current_password' => 'كلمة المرور الحالية', 'avatar' => 'الصورة',
        ]);

        if ($request->hasFile('avatar')) {
            delete_upload($user->avatar);
            $data['avatar'] = store_upload($request->file('avatar'), 'avatars');
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        unset($data['current_password']);

        $user->update($data);
        ActivityLog::record('update', 'تحديث الملف الشخصي', $user);

        return back()->with('success', 'تم تحديث ملفك الشخصي.');
    }
}
