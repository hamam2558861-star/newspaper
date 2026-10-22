<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;

class FeedController extends Controller
{
    public function rss()
    {
        $items = Article::published()->with('category:id,name')->latest('published_at')->limit(30)->get();

        return response()->view('site.rss', ['items' => $items])->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function sitemap()
    {
        return response()->view('site.sitemap', [
            'articles' => Article::published()->latest('published_at')->limit(2000)->get(['id', 'slug', 'published_at', 'updated_at']),
            'categories' => Category::active()->get(['slug', 'updated_at']),
            'pages' => Page::where('is_active', true)->get(['slug', 'updated_at']),
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
