<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'user_id', 'reviewed_by', 'kicker', 'title', 'slug', 'excerpt', 'body', 'image',
        'image_caption', 'video_url', 'type', 'status', 'dateline', 'source', 'is_breaking', 'is_featured',
        'allow_comments', 'views', 'published_at', 'reject_reason', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** المواد المنشورة والظاهرة للجمهور (تستثني المجدولة مستقبلاً). */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')->where('published_at', '<=', now());
    }

    public function scopeOfType(Builder $q, string $type): Builder
    {
        return $q->where('type', $type);
    }

    public function isLive(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->lte(now());
    }

    public function isScheduled(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->gt(now());
    }

    public function url(): string
    {
        return route('article.show', [$this->id, $this->slug]);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? upload_url($this->image) : null;
    }

    public function getTypeLabelAttribute(): string
    {
        return config('newsroom.article_types.' . $this->type, $this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->isScheduled() ? 'مجدول' : config('newsroom.article_statuses.' . $this->status, $this->status);
    }

    public function getReadingMinutesAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body), 0, 'ابتثجحخدذرزسشصضطظعغفقكلمنهويىءآأإؤئةـ') / 200) ?: 1);
    }

    /** يحوّل رابط يوتيوب إلى رابط تضمين. */
    public function getVideoEmbedAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }
        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([\w-]{11})~', $this->video_url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        return null;
    }

    public static function makeSlug(string $title): string
    {
        $slug = trim(preg_replace('/[^\p{L}\p{N}]+/u', '-', $title), '-') ?: 'article';

        return mb_substr($slug, 0, 100);
    }
}
