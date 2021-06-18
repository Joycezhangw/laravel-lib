<?php


namespace JoyceZ\LaravelLib\Validation;


use Illuminate\Support\Arr;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

/**
 * 表单验证
 * Class AbstractValidator
 * @package JoyceZ\LaravelLib\Validation
 */
abstract class AbstractValidator
{
    protected $validator;

    /**
     * 验证规则
     * @var array
     */
    protected $rules = [];

    /**
     * 自定义验证错误消息
     * @var array
     */
    protected $messages = [];

    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * 获取验证规则
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * 获取验证自定义错误信息
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }


    public function make(array $attributes)
    {
        return $this->validator->make($attributes, $this->rules, $this->messages);
    }
}