<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;

class TagController extends Controller
{
    public function show(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        return view('site.listing', [
            'title' => 'وسم: ' . $tag->name,
            'articles' => $tag->articles()->published()->with('category:id,name,slug,color')->latest('published_at')->paginate((int) setting('articles_per_page', 12)),
            'mostRead' => Article::published()->orderByDesc('views')->limit(5)->get(),
        ]);
    }
}
