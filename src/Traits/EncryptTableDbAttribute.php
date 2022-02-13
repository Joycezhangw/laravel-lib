<?php

namespace JoyceZ\LaravelLib\Traits;

use JoyceZ\LaravelLib\Aop\AopCrypt;

/**
 * 对数据进行加密
 * Trait EncryptTableDbAttribute
 * @package JoyceZ\LaravelLib\Traits
 */
trait EncryptTableDbAttribute
{
    /**
     * 设置字段加密属性
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (is_null($value) || !in_array($key, $this->encryptTable)) {
            return parent::setAttribute($key, $value);
        }
        if ($this->isJsonCastable($key) && !is_null($value)) {
            $value = $this->castAttributeAsJson($key, $value);
        }
        $value = $this->encrypt($value);
        return parent::setAttribute($key, $value);
    }

    /**
     * Get a plain attribute (not a relationship).
     *
     * @param string $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);

        // If the attribute has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        // 在强制转换前解密
        if (in_array($key, $this->encryptTable) && !is_null($value) && $value !== '') {
            $value = $this->decrypt($value);
        }

        // If the attribute exists within the cast array, we will convert it to
        // an appropriate native PHP type dependant upon the associated value
        // given with the key in the pair. Dayle made this comment line up.
        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        // If the attribute is listed as a date, we will convert it to a DateTime
        // instance on retrieval, which makes it quite convenient to work with
        // date fields without having to create a mutator for each property.
        if (in_array($key, $this->getDates()) &&
            !is_null($value)) {
            return $this->asDateTime($value);
        }

        return $value;
    }


    /**
     * 将模型的属性转换为数组
     *
     * @return array
     */
    public function attributesToArray(): array
    {
        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
        $attributes = $this->addDateAttributesToArray(
            $attributes = $this->getArrayableAttributes()
        );

        $attributes = $this->addMutatedAttributesToArray(
            $attributes, $mutatedAttributes = $this->getMutatedAttributes()
        );

        // decrypt attributes before casts
        $attributes = $this->decryptAttributes($attributes);

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @return array
     */
    private function decryptAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $value) {
            if (!in_array($key, $this->encryptTable) || is_null($value) || $value === '') {
                continue;
            }

            $attributes[$key] = $this->decrypt($value);
        }

        return $attributes;
    }

    /**
     * 加密
     * @param mixed $value
     * @return string
     */
    private function encrypt($value)
    {
        return (new AopCrypt())->withScrectKey(env('APP_KEY'))->encrypt($value);
    }

    /**
     * 解密
     * @param mixed $value
     * @return mixed
     */
    public function decrypt($value)
    {
        return (new AopCrypt())->withScrectKey(env('APP_KEY'))->decrypt($value);
    }
}
