<?php


namespace JoyceZ\LaravelLib\Generators;


class EnumGenerator extends Generator
{
    protected $stub = 'enum';

    public function getPathConfigNode()
    {
        return 'enums';
    }

    public function getRootNamespace()
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Enum.php';
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
