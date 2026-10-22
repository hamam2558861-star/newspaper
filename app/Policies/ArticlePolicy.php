<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    private const AUTHOR_EDITABLE = ['draft', 'rejected', 'pending'];

    public function update(User $user, Article $article): bool
    {
        if ($user->hasPermission('articles.edit_all')) {
            return true;
        }

        return $user->hasPermission('articles.edit_own')
            && $article->user_id === $user->id
            && in_array($article->status, self::AUTHOR_EDITABLE, true);
    }

    public function delete(User $user, Article $article): bool
    {
        if ($user->hasPermission('articles.delete')) {
            return true;
        }

        return $article->user_id === $user->id && $article->status === 'draft' && $user->hasPermission('articles.edit_own');
    }

    /** إرسال للمراجعة: صاحب المادة فقط وهي مسودة أو مرفوضة. */
    public function submit(User $user, Article $article): bool
    {
        return $article->user_id === $user->id && in_array($article->status, ['draft', 'rejected'], true);
    }
}
