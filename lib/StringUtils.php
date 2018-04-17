<?php

namespace Bcl\EasyPdfCloud;

use function mb_strlen;

class StringUtils
{
    public static function length($string, $encoding = Constraints::UTF_8)
    {
        return mb_strlen($string, $encoding);
    }

    public static function isEmpty($string, $encoding = Constraints::UTF_8)
    {
        return 0 === self::length($string, $encoding = Constraints::UTF_8);
    }
}