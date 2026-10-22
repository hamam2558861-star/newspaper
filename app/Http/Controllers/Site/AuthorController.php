<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;

class AuthorController extends Controller
{
    public function show(User $user)
    {
        abort_unless($user->is_active && $user->articles()->published()->exists(), 404);

        return view('site.author', [
            'author' => $user->load('role'),
            'articles' => $user->articles()->published()->with('category:id,name,slug,color')->latest('published_at')->paginate((int) setting('articles_per_page', 12)),
            'mostRead' => Article::published()->orderByDesc('views')->limit(5)->get(),
        ]);
    }
}
