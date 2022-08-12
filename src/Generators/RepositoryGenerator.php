<?php


namespace JoyceZ\LaravelLib\Generators;


class RepositoryGenerator extends Generator
{
    protected $stub = 'repository';

    public function getPathConfigNode()
    {
        return 'repositories';
    }

    /**
     * 获取命名空间
     * @return mixed|string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace() . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Repo.php';
    }

    public function getBasePath()
    {
        return config('landao.generator.basePath', app()->path());
    }

    public function getReplacements()
    {
        return array_merge(parent::getReplacements(), [
            'interfaces' => isset($this->options['interfaces']) ? $this->options['interfaces'] : '',
            'model' => isset($this->options['model']) ? $this->options['model'] : ''
        ]);
    }


}
