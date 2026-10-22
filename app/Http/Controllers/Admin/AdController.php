<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdController extends Controller
{
    public function index()
    {
        return view('admin.ads.index', ['ads' => Ad::latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.ads.form', ['ad' => new Ad(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $ad = Ad::create($this->data($request));
        ActivityLog::record('create', "إضافة إعلان: {$ad->title}", $ad);

        return redirect()->route('admin.ads.index')->with('success', 'تمت إضافة الإعلان.');
    }

    public function edit(Ad $ad)
    {
        return view('admin.ads.form', compact('ad'));
    }

    public function update(Request $request, Ad $ad)
    {
        $ad->update($this->data($request, $ad));
        ActivityLog::record('update', "تعديل إعلان: {$ad->title}", $ad);

        return redirect()->route('admin.ads.index')->with('success', 'تم تحديث الإعلان.');
    }

    public function destroy(Ad $ad)
    {
        delete_upload($ad->image);
        ActivityLog::record('delete', "حذف إعلان: {$ad->title}", $ad);
        $ad->delete();

        return back()->with('success', 'تم حذف الإعلان.');
    }

    private function data(Request $request, ?Ad $ad = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'position' => ['required', Rule::in(array_keys(config('newsroom.ad_positions')))],
            'image' => ['nullable', 'image', 'max:3072'],
            'url' => ['nullable', 'url', 'max:500'],
            'code' => ['nullable', 'string', 'max:5000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ], [], ['title' => 'عنوان الإعلان', 'position' => 'الموضع', 'image' => 'الصورة', 'ends_at' => 'تاريخ الانتهاء']);

        if ($request->hasFile('image')) {
            delete_upload($ad?->image);
            $data['image'] = store_upload($request->file('image'), 'ads');
        } else {
            unset($data['image']);
        }
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
