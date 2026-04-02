<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create articles.
     * Authorization is handled by API key middleware.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the article.
     * Checks ownership via the first translation's created_by field.
     */
    public function update(User $user, Article $article): bool
    {
        $translation = $article->translations->first();
        return $translation && $user->id === $translation->created_by;
    }

    /**
     * Determine whether the user can delete the article.
     * Checks ownership via the first translation's created_by field.
     */
    public function delete(User $user, Article $article): bool
    {
        $translation = $article->translations->first();
        return $translation && $user->id === $translation->created_by;
    }
}
