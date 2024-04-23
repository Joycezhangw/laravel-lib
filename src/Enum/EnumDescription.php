<?php
declare (strict_types=1);

namespace JoyceZ\LaravelLib\Enum;

use Attribute;

/**
 * PHP8 枚举注解
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class EnumDescription
{
    private $value;

    public function __construct($value = '')
    {
        $this->value = $value;
    }
}