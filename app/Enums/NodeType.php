<?php

namespace App\Enums;

enum NodeType: string
{
    case ARTICLE = 'article';
    case USER_AGREEMENT = 'user_agreement';

    /**
     * Get the human-readable label for this enum case.
     */
    public function label(): string
    {
        return match ($this) {
            self::ARTICLE => 'Article',
            self::USER_AGREEMENT => 'User Agreement',
        };
    }

    /**
     * Check if this is the ARTICLE case.
     */
    public function isArticle(): bool
    {
        return $this === self::ARTICLE;
    }
}
