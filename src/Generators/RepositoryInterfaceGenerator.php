<?php


namespace JoyceZ\LaravelLib\Generators;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RepositoryInterfaceGenerator extends Generator
{
    protected $stub = 'interface';

    public function getPathConfigNode()
    {
        return 'interfaces';
    }

    public function getRootNamespace()
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * 对 interface 特殊处理
     * @return array
     */
    public function resetSegments()
    {
        $segments = $this->getSegments();
        array_splice($segments, count($segments) - 1, 0, Str::ucfirst($this->getPathConfigNode()));
        $segments[count($segments) - 1] = 'I' . Arr::last($segments);
        return $segments;
    }

    public function getNamespace()
    {
        $segments = $this->resetSegments();
        array_pop($segments);
        $rootNamespace = $this->getRootNamespace();
        if ($rootNamespace == false) {
            return null;
        }

        return rtrim($rootNamespace . '\\' . implode('\\', $segments), '\\') . ';';
    }

    public function getPath()
    {
        //重构生成路径
        $restName = implode('/', $this->resetSegments());
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . DIRECTORY_SEPARATOR . $restName . '.php';
    }

    public function getBasePath()
    {
        return config('landao.generator.basePath', app()->path());
    }

    public function getReplacements()
    {
        return array_merge(parent::getReplacements(), []);
    }

}
