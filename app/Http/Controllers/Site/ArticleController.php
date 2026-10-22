<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function show(Request $request, Article $article, ?string $slug = null)
    {
        $isPreview = false;
        if (! $article->isLive()) {
            abort_unless($request->user()?->can('update', $article) || $request->user()?->can('articles.publish'), 404);
            $isPreview = true;
        }

        // تعديل الرابط إلى الصيغة القياسية
        if (! $isPreview && $slug !== $article->slug) {
            return redirect()->to($article->url(), 301);
        }

        if (! $isPreview && ! $request->session()->has("viewed.{$article->id}")) {
            $article->increment('views');
            $request->session()->put("viewed.{$article->id}", true);
        }

        $article->load(['category:id,name,slug,color,parent_id', 'author:id,name,avatar,job_title,bio,twitter', 'tags']);

        $related = Article::published()->where('category_id', $article->category_id)->whereKeyNot($article->id)
            ->with('category:id,name,slug,color')->latest('published_at')->limit(4)->get();

        $more = Article::published()->whereKeyNot($article->id)->whereNotIn('id', $related->pluck('id'))
            ->with('category:id,name,slug,color')->orderByDesc('views')->limit(5)->get();

        return view('site.article', [
            'article' => $article,
            'related' => $related,
            'mostRead' => $more,
            'comments' => $article->comments()->approved()->latest()->get(),
            'isPreview' => $isPreview,
        ]);
    }

    public function comment(Request $request, Article $article)
    {
        abort_unless($article->isLive() && $article->allow_comments && setting('comments_enabled', '1') === '1', 404);

        // حقل مصيدة للروبوتات
        if ($request->filled('website')) {
            return back();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120'],
            'body' => ['required', 'string', 'min:3', 'max:1500'],
        ], [], ['name' => 'الاسم', 'email' => 'البريد الإلكتروني', 'body' => 'التعليق']);

        $auto = setting('comments_auto_approve', '0') === '1';
        $article->comments()->create($data + ['status' => $auto ? 'approved' : 'pending', 'ip' => $request->ip()]);

        return redirect()->to($article->url() . '#comments')->with('comment_ok', $auto ? 'تم نشر تعليقك.' : 'شكراً لك، سيظهر تعليقك بعد مراجعته من فريق التحرير.');
    }
}
