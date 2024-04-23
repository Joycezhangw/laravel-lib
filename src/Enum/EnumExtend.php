<?php
// +----------------------------------------------------------------------
// | 通用类包
// +----------------------------------------------------------------------
// | Copyright (c) 2020 https://qilindao.github.io/docs/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: joyecZhang <zhangwei762@163.com>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace JoyceZ\LaravelLib\Enum;

use JoyceZ\LaravelLib\Exceptions\EnumException;

/**
 * php8 枚举扩展
 */
trait EnumExtend
{
    /**
     * 获取全部枚举
     * 以 ['key'=>'value',...] 形式返回枚举内容
     * @return array
     */
    public static function getMap(): array
    {
        $enums = [];
        foreach (self::cases() as $enum) {
            $enums[$enum->name] = $enum->value;
        }
        return $enums;
    }

    /**
     * 返回枚举所有keys
     * @return array
     */
    public static function getKeys(): array
    {
        return array_map(fn($enum): string => $enum->name, self::cases());
    }

    /**
     * 返回枚举所有值
     * @return array
     */
    public static function getValues(): array
    {
        return array_map(fn($enum): mixed => $enum->value, self::cases());
    }

    /**
     * 判断key是否包含在枚举中
     * @param string $value
     * @return bool
     */
    public static function include(string $value): bool
    {
        return in_array($value, self::getKeys());
    }

    /**
     * 判断多个key是否包含在枚举中
     * @param array $names
     * @return bool
     */
    public static function includeAll(array $names): bool
    {
        $enums = self::getKeys();
        foreach ($enums as $enum) {
            if (!in_array($names, $enums)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 判断持久层返回的是否相同
     * @param string $value
     * @return bool
     */
    public function equal(string $value): bool
    {
        return self::tryFrom($value) === $this;
    }

    /**
     * 获取枚举说明
     * @return string
     * @throws EnumException
     */
    public function getEnumLabel(): string
    {
        $ref = new \ReflectionEnumUnitCase(self::class, $this->name);
        $attributes = $ref->getAttributes();
        if (!$attributes) throw new EnumException('未配置 Enum 注解说明');
        foreach ($attributes as $attribute) {
            $args = $attribute->getArguments();
            return $args[0];
        }
        return '';
    }

    /**
     * 获取枚举值和说明数组
     * @return array
     * @throws EnumException
     */
    public static function getEnumMapLabel(): array
    {
        $enums = [];
        foreach (self::cases() as $enum) {
            $enums[$enum->value] = $enum->getEnumLabel();
        }
        return $enums;
    }
}
