<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = ['article_id', 'name', 'email', 'body', 'status', 'ip'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }
}
