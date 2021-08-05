<?php

namespace Overfish\Beiwo\Foundation\Enums;

use MyCLabs\Enum\Enum as BaseEnum;

class Enum extends BaseEnum
{
    use Traits\ReflectionDescription;

    protected static $description = [];

    /**
     * @param $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return
            static::$description[$value] ??
            static::getReflectionDescription(static::search($value)) ??
            '';
    }
}
