<?php

namespace App\Enums;

enum TranslationStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case UNPUBLISHED = 'unpublished';

    /**
     * Get the human-readable label for this enum case.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::UNPUBLISHED => 'Unpublished',
        };
    }

    /**
     * Check if this is the PUBLISHED case.
     */
    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    /**
     * Get all enum values as an array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
