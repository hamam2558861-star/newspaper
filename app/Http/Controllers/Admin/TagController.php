<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::withCount('articles')
            ->when($request->query('q'), fn ($q, $s) => $q->where('name', 'like', "%$s%"))
            ->orderByDesc('articles_count')->paginate(30)->withQueryString();

        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.form', ['tag' => new Tag]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:60']]);
        $slug = Article::makeSlug($request->name);
        Tag::firstOrCreate(['slug' => $slug], ['name' => $request->name]);

        return redirect()->route('admin.tags.index')->with('success', 'تمت إضافة الوسم.');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.form', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate(['name' => ['required', 'string', 'max:60']]);
        $slug = Article::makeSlug($request->name);
        $request->validate(['name' => [Rule::unique('tags', 'name')->ignore($tag->id)]]);
        $tag->update(['name' => $request->name, 'slug' => $slug]);

        return redirect()->route('admin.tags.index')->with('success', 'تم تحديث الوسم.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return back()->with('success', 'تم حذف الوسم.');
    }
}
