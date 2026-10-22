<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'color', 'sort_order', 'show_in_menu', 'is_active'];

    protected $casts = ['show_in_menu' => 'boolean', 'is_active' => 'boolean'];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function url(): string
    {
        return route('category.show', $this->slug);
    }
}
