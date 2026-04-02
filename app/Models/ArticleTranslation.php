<?php

namespace App\Models;

use App\Enums\TranslationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleTranslation extends Model
{
    use SoftDeletes;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'article_translation_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'article_id',
        'language_code',
        'title',
        'path',
        'summary',
        'keywords',
        'content',
        'created_by',
        'modified_by',
        'status',
        'unpublished_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => TranslationStatus::class,
        'unpublished_at' => 'datetime',
    ];

    /**
     * Get the article this translation belongs to.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'article_id');
    }

    /**
     * Get the site language this translation belongs to.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(SiteLanguage::class, 'language_code', 'language_code');
    }
}
