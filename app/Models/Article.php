<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'article_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'node_type',
        'visibility',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'node_type' => 'string',
        'visibility' => 'string',
    ];

    /**
     * Get the translations for this article.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id', 'article_id');
    }
}
