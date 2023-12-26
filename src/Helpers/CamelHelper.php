<?php
// +----------------------------------------------------------------------
// | 通用类包
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.hmall.com.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: joyecZhang <zhangwei762@163.com>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace JoyceZ\LaravelLib\Helpers;


use Illuminate\Support\Str;

class CamelHelper
{
    /**
     * 循环迭代将数组键驼峰转下划线
     * @param $arr
     * @return array
     */
    public static function recursiveConvertParameterNameCase($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        $params = [];
        foreach ($arr as $key => $value) {
            if (!is_int($key)) {
                if (is_array($value)) {
                    $params[Str::snake($key)] = self::recursiveConvertParameterNameCase($value);
                } else {
                    $params[Str::snake($key)] = $value;
                }
            } elseif (is_array($value)) {
                $params[$key] = self::recursiveConvertParameterNameCase($value);
            } else {
                $params[Str::snake($key)] = $value;
            }
        }
        return $params;
    }

    /**
     * 循环迭代将数组键转换位驼峰
     * @param $arr
     * @return array
     */
    public static function recursiveConvertNameCaseToCamel($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        $outArray = [];
        foreach ($arr as $key => $value) {
            if (!is_int($key)) {
                if (is_array($value)) {
                    $outArray[Str::camel($key)] = self::recursiveConvertNameCaseToCamel($value);
                } else {
                    $outArray[Str::camel($key)] = $value;
                }
            } elseif (is_array($value)) {
                $outArray[$key] = self::recursiveConvertNameCaseToCamel($value);
            } else {
                $outArray[Str::camel($key)] = $value;
            }
        }
        return $outArray;
    }

}