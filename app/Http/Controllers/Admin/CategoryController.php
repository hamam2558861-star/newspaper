<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::with('parent')->withCount('articles')->orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category(['color' => '#b91c1c', 'is_active' => true, 'show_in_menu' => true]), 'parents' => $this->parents()]);
    }

    public function store(Request $request)
    {
        $category = Category::create($this->data($request));
        ActivityLog::record('create', "إضافة قسم: {$category->name}", $category);

        return redirect()->route('admin.categories.index')->with('success', 'تمت إضافة القسم.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', ['category' => $category, 'parents' => $this->parents($category)]);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->data($request, $category));
        ActivityLog::record('update', "تعديل قسم: {$category->name}", $category);

        return redirect()->route('admin.categories.index')->with('success', 'تم تحديث القسم.');
    }

    public function destroy(Category $category)
    {
        if (Article::withTrashed()->where('category_id', $category->id)->exists() || $category->children()->exists()) {
            return back()->with('error', 'لا يمكن حذف قسم يحتوي على مواد أو أقسام فرعية. يمكنك إخفاءه بدلاً من ذلك.');
        }
        ActivityLog::record('delete', "حذف قسم: {$category->name}", $category);
        $category->delete();

        return back()->with('success', 'تم حذف القسم.');
    }

    private function parents(?Category $except = null)
    {
        return Category::whereNull('parent_id')->when($except, fn ($q) => $q->where('id', '!=', $except->id))->orderBy('sort_order')->get();
    }

    private function data(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('categories')->ignore($category?->id)],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [], ['name' => 'اسم القسم', 'slug' => 'الرابط', 'color' => 'اللون']);

        $data['slug'] = ($data['slug'] ?? null) ?: Article::makeSlug($data['name']);
        $data['parent_id'] = $data['parent_id'] ?? null;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['show_in_menu'] = $request->boolean('show_in_menu');
        $data['is_active'] = $request->boolean('is_active');
        if ($category && ($data['parent_id'] ?? null) == $category->id) {
            $data['parent_id'] = null;
        }

        return $data;
    }
}
