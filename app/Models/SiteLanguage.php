<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteLanguage extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'site_languages';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'language_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'language_code',
        'language_name',
    ];

    /**
     * Get the article translations for this language.
     */
    public function articleTranslations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class, 'language_code', 'language_code');
    }
}
