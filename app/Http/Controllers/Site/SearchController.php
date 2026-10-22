<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $term = trim((string) $request->query('q'));
        $category = $request->query('category');
        $articles = null;

        if (mb_strlen($term) >= 2) {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';
            $articles = Article::published()
                ->when($category, fn ($q) => $q->where('category_id', $category))
                ->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('excerpt', 'like', $like)->orWhere('body', 'like', $like)->orWhere('kicker', 'like', $like))
                ->with('category:id,name,slug,color')
                ->orderByRaw('CASE WHEN title LIKE ? THEN 0 ELSE 1 END', [$like])
                ->latest('published_at')
                ->paginate(12)->withQueryString();
        }

        return view('site.search', [
            'term' => $term,
            'articles' => $articles,
            'categories' => Category::active()->orderBy('sort_order')->get(['id', 'name']),
            'mostRead' => Article::published()->orderByDesc('views')->limit(5)->get(),
        ]);
    }
}
