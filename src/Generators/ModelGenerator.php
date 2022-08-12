<?php


namespace JoyceZ\LaravelLib\Generators;


class ModelGenerator extends Generator
{
    protected $stub = 'model';

    public function getPathConfigNode()
    {
        return 'models';
    }

    public function getRootNamespace()
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Model.php';
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