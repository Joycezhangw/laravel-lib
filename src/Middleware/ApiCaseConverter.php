<?php

namespace JoyceZ\LaravelLib\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * 1、将前端发送过来的请求参数驼峰转下划线
 * 2、后端响应参数下划线转驼峰
 * Class ApiCaseConverter
 * @package App\Http\Middleware
 */
class ApiCaseConverter
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->convertRequestNameCase($request);
        $response = $next($request);
        $this->convertResponseNameCase($response);
        return $response;
    }

    /**
     * 请求数据驼峰转下划线
     * @param $request
     */
    private function convertRequestNameCase($request)
    {
        $this->convertParameterNameCase($request->request);;
        $this->convertParameterNameCase($request->query);;
        $this->convertParameterNameCase($request->files);;
        $this->convertParameterNameCase($request->cookies);;
    }

    /**
     * 将驼峰命名转下划线命名
     * @param ParameterBag $parameterBag
     */
    private function convertParameterNameCase($parameterBag)
    {
        $parameters = $parameterBag->all();
        $parameterBag->replace($this->recursiveConvertParameterNameCase($parameters));
    }

    /**
     * 循环迭代将数组键驼峰转下划线
     * @param $arr
     * @return array
     */
    private function recursiveConvertParameterNameCase($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        $params = [];
        foreach ($arr as $key => $value) {
            if (!is_int($key)) {
                if (is_array($value)) {
                    $params[Str::snake($key)] = $this->recursiveConvertParameterNameCase($value);
                } else {
                    $params[Str::snake($key)] = $value;
                }
            } elseif (is_array($value)) {
                $params[$key] = $this->recursiveConvertParameterNameCase($value);
            } else {
                $params[Str::snake($key)] = $value;
            }
        }
        return $params;
    }

    /**
     * 响应数据下划线转驼峰
     * @param $response
     */
    private function convertResponseNameCase($response)
    {
        $content = $response->getContent();
        $json = json_decode($content, true);
        if (is_array($json)) {
            $json = $this->recursiveConvertNameCaseToCamel($json);
            $response->setContent(json_encode($json));
        }
    }

    /**
     * 循环迭代将数组键转换位驼峰
     * @param $arr
     * @return array
     */
    private function recursiveConvertNameCaseToCamel($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        $outArray = [];
        foreach ($arr as $key => $value) {
            if (!is_int($key)) {
                if (is_array($value)) {
                    $outArray[Str::camel($key)] = $this->recursiveConvertNameCaseToCamel($value);
                } else {
                    $outArray[Str::camel($key)] = $value;
                }
            } elseif (is_array($value)) {
                $outArray[$key] = $this->recursiveConvertNameCaseToCamel($value);
            } else {
                $outArray[Str::camel($key)] = $value;
            }
        }
        return $outArray;
    }
}