<?php

namespace JoyceZ\LaravelLib\Model\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;
use JoyceZ\LaravelLib\Security\AopEncryptDataIndex;

/**
 * 可模糊搜索的加密字段进行拆字加密，使其支持模糊搜索
 */
class EncryptDataIndex implements CastsInboundAttributes
{
    public function __construct(protected string $indexType = '')
    {

    }

    /**
     * 转换成将要存储的值
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (trim($value) == '') {
            return "";
        }
        $encryptDataIndex = new AopEncryptDataIndex();
        return trim($this->indexType) != '' ? $encryptDataIndex->encrypt($value, $this->indexType) : $value;
    }

}