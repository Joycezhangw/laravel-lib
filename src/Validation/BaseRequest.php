<?php


namespace JoyceZ\LaravelLib\Validation;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * 表单提交验证
 * Class BaseRequest
 * @package JoyceZ\LaravelLib\Validation
 */
abstract class BaseRequest extends FormRequest
{
    /**
     * 定义验证规则
     * @return array
     */
    public function rules()
    {
        $rule_action = 'getRulesBy' . ucfirst($this->route()->getActionMethod());

        if (method_exists($this, $rule_action))
            return $this->$rule_action();

        return $this->getDefaultRules();
    }

    /**
     * 默认验证规则
     * @return array
     */
    protected function getDefaultRules()
    {
        return [];
    }

    /**
     * 验证消息通过，json抛出，api开发
     * @param Validator $validator
     * @throws \HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'code' => -1,
            'message' => $validator->errors()->first()
        ]));
    }
}