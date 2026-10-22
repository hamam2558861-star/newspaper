<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;

class HomeController extends Controller
{
    private const CARD = ['id', 'category_id', 'user_id', 'kicker', 'title', 'slug', 'excerpt', 'image', 'type', 'views', 'published_at', 'is_breaking', 'video_url'];

    public function index()
    {
        $with = ['category:id,name,slug,color'];

        $hero = Article::published()->where('is_featured', true)->with($with)->latest('published_at')->limit(5)->get(self::CARD);
        if ($hero->count() < 5) {
            $fill = Article::published()->whereNotIn('id', $hero->pluck('id'))->with($with)->latest('published_at')->limit(5 - $hero->count())->get(self::CARD);
            $hero = $hero->concat($fill);
        }

        $latest = Article::published()->whereNotIn('id', $hero->pluck('id'))->with($with)->latest('published_at')->limit(10)->get(self::CARD);

        $sections = Category::active()->whereNull('parent_id')->where('show_in_menu', true)->orderBy('sort_order')->limit(6)->get()
            ->map(function (Category $c) use ($hero) {
                $ids = $c->children()->pluck('id')->push($c->id);
                $c->setRelation('items', Article::published()->whereIn('category_id', $ids)->latest('published_at')->limit(5)->get(self::CARD));

                return $c;
            })->filter(fn ($c) => $c->items->isNotEmpty());

        return view('site.home', [
            'hero' => $hero,
            'latest' => $latest,
            'sections' => $sections,
            'mostRead' => $this->mostRead(),
            'opinions' => Article::published()->ofType('opinion')->with(['author:id,name,avatar,job_title'])->latest('published_at')->limit(4)->get(),
            'videos' => Article::published()->ofType('video')->with($with)->latest('published_at')->limit(4)->get(self::CARD),
            'trendingTags' => Tag::withCount(['articles' => fn ($q) => $q->published()])->orderByDesc('articles_count')->limit(12)->get()->filter(fn ($t) => $t->articles_count > 0),
        ]);
    }

    public function latest()
    {
        return view('site.listing', [
            'title' => 'آخر الأخبار',
            'articles' => Article::published()->with('category:id,name,slug,color')->latest('published_at')->paginate((int) setting('articles_per_page', 12)),
            'mostRead' => $this->mostRead(),
        ]);
    }

    public function opinions()
    {
        return view('site.listing', [
            'title' => 'الرأي والمقالات',
            'articles' => Article::published()->ofType('opinion')->with(['category:id,name,slug,color', 'author:id,name,avatar'])->latest('published_at')->paginate((int) setting('articles_per_page', 12)),
            'mostRead' => $this->mostRead(),
        ]);
    }

    public function videos()
    {
        return view('site.listing', [
            'title' => 'فيديو',
            'articles' => Article::published()->ofType('video')->with('category:id,name,slug,color')->latest('published_at')->paginate((int) setting('articles_per_page', 12)),
            'mostRead' => $this->mostRead(),
        ]);
    }

    private function mostRead()
    {
        $q = Article::published()->with('category:id,name,slug,color');
        $week = (clone $q)->where('published_at', '>=', now()->subDays(7))->orderByDesc('views')->limit(6)->get(self::CARD);

        return $week->count() >= 3 ? $week : $q->orderByDesc('views')->limit(6)->get(self::CARD);
    }
}
