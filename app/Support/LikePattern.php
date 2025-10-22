<?php

namespace App\Support;

/**
 * Helper for safely building SQL LIKE patterns without exposing the
 * application to wildcard or injection abuse.
 */
class LikePattern
{
    /**
     * Escape user supplied text for usage inside a LIKE clause and wrap it
     * with leading and trailing percent signs so that the final pattern does a
     * contains search.
     */
    public static function contains(string $value): string
    {
        return '%' . self::escape($value) . '%';
    }

    /**
     * Escape special characters that have meaning inside LIKE patterns.
     */
    public static function escape(string $value): string
    {
        return addcslashes($value, "\\%_");
    }
}

