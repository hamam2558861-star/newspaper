<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        $category = Category::active()->where('slug', $slug)->with(['children' => fn ($q) => $q->where('is_active', true), 'parent'])->firstOrFail();
        $ids = $category->children->pluck('id')->push($category->id);

        $articles = Article::published()->whereIn('category_id', $ids)
            ->with('category:id,name,slug,color')->latest('published_at')
            ->paginate((int) setting('articles_per_page', 12));

        $mostRead = Article::published()->whereIn('category_id', $ids)->orderByDesc('views')->limit(5)->get();

        return view('site.category', compact('category', 'articles', 'mostRead'));
    }
}
