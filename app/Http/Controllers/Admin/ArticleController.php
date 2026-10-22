<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $seeAll = $user->can('articles.edit_all') || $user->can('articles.publish');

        $q = Article::query()->with(['category', 'author']);
        if (! $seeAll || $request->boolean('mine')) {
            $q->where('user_id', $user->id);
        }
        if ($status = $request->query('status')) {
            $status === 'scheduled'
                ? $q->where('status', 'published')->where('published_at', '>', now())
                : $q->where('status', $status);
        }
        if ($cat = $request->query('category')) {
            $q->where('category_id', $cat);
        }
        if ($type = $request->query('type')) {
            $q->where('type', $type);
        }
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('title', 'like', "%$s%")->orWhere('kicker', 'like', "%$s%"));
        }

        return view('admin.articles.index', [
            'articles' => $q->latest('updated_at')->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('sort_order')->get(),
            'seeAll' => $seeAll,
        ]);
    }

    public function create()
    {
        return view('admin.articles.form', [
            'article' => new Article(['type' => 'news', 'allow_comments' => true]),
            'categories' => Category::active()->orderBy('sort_order')->get(),
            'tagsText' => '',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $article = new Article;
        $article->user_id = $request->user()->id;
        $this->fill($article, $data, $request);

        ActivityLog::record('create', "إنشاء مادة: {$article->title}", $article);

        return redirect()->route('admin.articles.edit', $article)->with('success', $this->flashFor($article));
    }

    public function edit(Article $article)
    {
        Gate::authorize('update', $article);

        return view('admin.articles.form', [
            'article' => $article->load('tags'),
            'categories' => Category::active()->orderBy('sort_order')->get(),
            'tagsText' => $article->tags->pluck('name')->implode('، '),
        ]);
    }

    public function update(Request $request, Article $article)
    {
        Gate::authorize('update', $article);
        $data = $this->validated($request);
        $this->fill($article, $data, $request);

        ActivityLog::record('update', "تعديل مادة: {$article->title}", $article);

        return back()->with('success', $this->flashFor($article));
    }

    public function destroy(Article $article)
    {
        Gate::authorize('delete', $article);
        ActivityLog::record('delete', "حذف مادة: {$article->title}", $article);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'تم نقل المادة إلى المحذوفات.');
    }

    /** إرسال المادة للمراجعة من كاتبها. */
    public function submit(Article $article)
    {
        Gate::authorize('submit', $article);
        $article->update(['status' => 'pending', 'reject_reason' => null]);
        ActivityLog::record('submit', "إرسال للمراجعة: {$article->title}", $article);

        return back()->with('success', 'تم إرسال المادة إلى المراجعة التحريرية.');
    }

    /** قرارات المراجعة: نشر / رفض / أرشفة / إرجاع لمسودة. */
    public function workflow(Request $request, Article $article)
    {
        Gate::authorize('articles.publish');

        $request->validate([
            'decision' => ['required', Rule::in(['publish', 'reject', 'archive', 'draft'])],
            'reject_reason' => ['required_if:decision,reject', 'nullable', 'string', 'max:1000'],
        ], ['reject_reason.required_if' => 'يرجى كتابة سبب الرفض ليطّلع عليه الكاتب.']);

        switch ($request->decision) {
            case 'publish':
                $article->status = 'published';
                $article->published_at ??= now();
                $article->reject_reason = null;
                $label = 'اعتماد ونشر';
                break;
            case 'reject':
                $article->status = 'rejected';
                $article->reject_reason = $request->reject_reason;
                $label = 'رفض';
                break;
            case 'archive':
                $article->status = 'archived';
                $label = 'أرشفة';
                break;
            default:
                $article->status = 'draft';
                $label = 'إرجاع إلى مسودة';
        }
        $article->reviewed_by = $request->user()->id;
        $article->save();

        ActivityLog::record($request->decision, "$label: {$article->title}", $article);

        return back()->with('success', "تم تنفيذ الإجراء: $label.");
    }

    /** رفع صورة من داخل المحرر النصي. */
    public function uploadImage(Request $request)
    {
        $request->validate(['image' => ['required', 'image', 'max:5120']]);

        return response()->json(['url' => upload_url(store_upload($request->file('image'), 'content/' . now()->format('Y/m')))]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', Rule::in(array_keys(config('newsroom.article_types')))],
            'kicker' => ['nullable', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'dateline' => ['nullable', 'string', 'max:80'],
            'source' => ['nullable', 'string', 'max:120'],
            'tags' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'action' => ['nullable', Rule::in(['draft', 'submit', 'publish'])],
        ], [], [
            'category_id' => 'القسم', 'title' => 'العنوان', 'body' => 'نص المادة', 'image' => 'الصورة', 'type' => 'نوع المادة',
            'video_url' => 'رابط الفيديو', 'published_at' => 'موعد النشر',
        ]);
    }

    private function fill(Article $article, array $data, Request $request): void
    {
        $user = $request->user();
        $canPublish = $user->can('articles.publish');
        $isNew = ! $article->exists;

        $article->fill([
            'category_id' => $data['category_id'],
            'type' => $data['type'],
            'kicker' => $data['kicker'] ?? null,
            'title' => $data['title'],
            'excerpt' => ($data['excerpt'] ?? null) ?: Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags($data['body']))), 220),
            'body' => sanitize_html($data['body']),
            'image_caption' => $data['image_caption'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'dateline' => $data['dateline'] ?? null,
            'source' => $data['source'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'allow_comments' => $request->boolean('allow_comments'),
        ]);

        if ($request->hasFile('image')) {
            delete_upload($article->image);
            $article->image = store_upload($request->file('image'), 'articles/' . now()->format('Y/m'));
        } elseif ($request->boolean('remove_image')) {
            delete_upload($article->image);
            $article->image = null;
        }

        // خيارات حصرية لمن يملك صلاحية النشر
        if ($canPublish) {
            $article->is_breaking = $request->boolean('is_breaking');
            $article->is_featured = $request->boolean('is_featured');
            $article->published_at = $data['published_at'] ?? $article->published_at;
        }

        $action = $data['action'] ?? null;
        if ($action === 'publish' && $canPublish) {
            $article->status = 'published';
            $article->published_at ??= now();
            $article->reviewed_by = $user->id;
            $article->reject_reason = null;
        } elseif ($action === 'submit') {
            $article->status = 'pending';
            $article->reject_reason = null;
        } elseif ($isNew || $action === 'draft') {
            // المواد المنشورة والمؤرشفة لا تتحول إلى مسودة عند الحفظ العادي
            if ($isNew || in_array($article->status, ['draft', 'rejected', 'pending'], true)) {
                $article->status = 'draft';
            }
        }

        $article->slug = Article::makeSlug($article->title);
        $article->save();

        $names = collect(preg_split('/[,،]+/u', (string) ($data['tags'] ?? '')))
            ->map(fn ($n) => trim($n))->filter()->unique()->take(12);
        $ids = $names->map(function ($name) {
            $slug = Article::makeSlug($name);

            return Tag::firstOrCreate(['slug' => $slug], ['name' => $name])->id;
        });
        $article->tags()->sync($ids);
    }

    private function flashFor(Article $article): string
    {
        return match (true) {
            $article->isScheduled() => 'تم حفظ المادة وجدولة نشرها في ' . ar_date($article->published_at, 'j F Y - H:i'),
            $article->status === 'published' => 'تم نشر المادة بنجاح.',
            $article->status === 'pending' => 'تم إرسال المادة إلى المراجعة.',
            default => 'تم حفظ المادة كمسودة.',
        };
    }
}
