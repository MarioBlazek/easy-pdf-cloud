<?php

declare(strict_types=1);

namespace Bcl\EasyPdfCloud;

use function mb_strlen;

class StringUtils
{
    /**
     * Returns length of multibyte string
     *
     * @param string $string
     * @param string $encoding
     *
     * @return int
     */
    public static function length(string $string, string $encoding = Constraints::UTF_8): int
    {
        return mb_strlen($string, $encoding);
    }

    /**
     * Checks if multibyte string is empty
     *
     * @param string $string
     * @param string $encoding
     *
     * @return bool
     */
    public static function isEmpty(string $string, string $encoding = Constraints::UTF_8): bool
    {
        return 0 === self::length($string, $encoding = Constraints::UTF_8);
    }
}