<?php
// +----------------------------------------------------------------------
// | 通用类包
// +----------------------------------------------------------------------
// | Copyright (c) 2020 https://qilindao.github.io/docs/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: joyecZhang <787027175@qq.com>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace JoyceZ\LaravelLib\Enum;

/**
 * 枚举 基础类
 * Class BaseEnum
 * @package JoyceZ\LaravelLib\Enum
 */
abstract class BaseEnum
{

    /**
     * 获取全部枚举
     * @return array
     */
    abstract public static function getMap(): array;

    /**
     * 根据key获取数据
     * @param $key
     * @return string
     */
    public static function getValue($key)
    {
        return static::getMap()[$key] ?? null;
    }

    /**
     * 获取所有key
     * @return array
     */
    public static function getKeys(): array
    {
        return array_keys(static::getMap());
    }

}