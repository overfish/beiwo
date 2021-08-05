<?php

namespace Overfish\Beiwo\Foundation\Enums\Traits;

use ReflectionClassConstant;

trait ReflectionDescription
{
    /**
     * 通过反射文档注释获取描述
     *
     * @param  string  $key
     * @return string
     */
    protected static function getReflectionDescription(string $key): string
    {
        if ($key) {
            $reflectionConstant = new ReflectionClassConstant(static::class, $key);
            $docComment = $reflectionConstant->getDocComment();
            if ($docComment) {
                return rtrim(ltrim($docComment, "/** \t\r\n"), "\t\r\n */");
            }
        }

        return '';
    }
}
