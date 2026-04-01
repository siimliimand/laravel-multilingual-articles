<?php

namespace App\Enums;

enum Visibility: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';

    /**
     * Get the human-readable label for this enum case.
     */
    public function label(): string
    {
        return match ($this) {
            self::PUBLIC => 'Public',
            self::PRIVATE => 'Private',
        };
    }

    /**
     * Check if this is the PUBLIC case.
     */
    public function isPublic(): bool
    {
        return $this === self::PUBLIC;
    }
}
