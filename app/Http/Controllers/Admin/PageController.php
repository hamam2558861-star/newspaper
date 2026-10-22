<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('title')->get()]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['is_active' => true, 'show_in_footer' => true])]);
    }

    public function store(Request $request)
    {
        $page = Page::create($this->data($request));
        ActivityLog::record('create', "إضافة صفحة: {$page->title}", $page);

        return redirect()->route('admin.pages.index')->with('success', 'تمت إضافة الصفحة.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->data($request, $page));
        ActivityLog::record('update', "تعديل صفحة: {$page->title}", $page);

        return redirect()->route('admin.pages.index')->with('success', 'تم تحديث الصفحة.');
    }

    public function destroy(Page $page)
    {
        ActivityLog::record('delete', "حذف صفحة: {$page->title}", $page);
        $page->delete();

        return back()->with('success', 'تم حذف الصفحة.');
    }

    private function data(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('pages')->ignore($page?->id)],
            'body' => ['required', 'string'],
        ], [], ['title' => 'العنوان', 'body' => 'المحتوى']);

        $data['slug'] = ($data['slug'] ?? null) ?: Article::makeSlug($data['title']);
        $data['body'] = sanitize_html($data['body']);
        $data['show_in_footer'] = $request->boolean('show_in_footer');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
