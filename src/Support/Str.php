<?php

declare(strict_types=1);

namespace Denosys\Core\Support;

use Symfony\Component\String\UnicodeString;

/**
 * String utility class for common string operations.
 *
 * This class provides a static interface to string manipulation methods
 * using the Symfony String component.
 */
class Str
{
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $haystack The string to be searched.
     * @param string|string[] $needles The substring(s) to search for at the start of $haystack.
     *
     * @return bool True if $haystack starts with any of the $needles, false otherwise.
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        $string = new UnicodeString($haystack);
        return $string->startsWith($needles);
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * If the search value is not found, the entire string will be returned.
     *
     * @param string $subject The string to search in.
     * @param string $search The value to search for in $subject.
     *
     * @return string The portion of $subject after the first occurrence of $search.
     */
    public static function after(string $subject, string $search): string
    {
        $string = new UnicodeString($subject);
        return $string->after($search)->toString();
    }
}
