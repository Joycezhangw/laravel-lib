<?php


namespace JoyceZ\LaravelLib\Generators;


use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JoyceZ\LaravelLib\Exceptions\FileAlreadyExistsException;

abstract class Generator
{

    protected $filesystem;

    protected $options;

    protected $stub;

    public function __construct(array $options = [])
    {
        $this->filesystem = new Filesystem();
        $this->options = $options;
    }

    public function getFilesystem()
    {
        return $this->filesystem;
    }

    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    public function getStub()
    {
        $path = __DIR__ . '/../resources/stubs/';
        return (new Stub($path . $this->stub . '.stub', $this->getReplacements()))->render();
    }

    public function getReplacements()
    {
        return [
            'class' => $this->getClass(),
            'namespace' => $this->getNamespace(),
            'root_namespace' => $this->getRootNamespace()
        ];
    }

    public function getBasePath()
    {
        return base_path();
    }

    /**
     * 获取名称，支持斜杆目录
     * @return string
     */
    public function getName()
    {
        $name = $this->name;
        if (Str::contains($this->name, '\\')) {
            $name = str_replace('\\', '/', $this->name);
        }
        if (Str::contains($this->name, '/')) {
            $name = str_replace('/', '/', $this->name);
        }
        return Str::studly(str_replace(' ', '/', ucwords(str_replace('/', ' ', $name))));
    }

    public function getPath()
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . $this->getName() . '.php';
    }

    public function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }

    public function getClass()
    {
        return Str::studly(class_basename($this->getName()));
    }

    public function getSegments()
    {
        return explode('/', $this->getName());
    }

    /**
     * 获取命名空间
     * @return mixed
     */
    public function getRootNamespace()
    {
        return config('landao.generator.rootNamespace', $this->getAppNamespace());
    }

    public function getConfigGeneratorClassPath($class, $directoryPath = false)
    {
        switch ($class) {
            case ('models' === $class):
                $path = config('landao.generator.paths.models', 'Models');
                break;
            case ('interfaces' === $class):
                $path = config('landao.generator.paths.interfaces', 'Interfaces');
                break;
            case ('repositories' === $class):
                $path = config('landao.generator.paths.repositories', 'Repositories');
                break;
            case ('enums' === $class):
                $path = config('landao.generator.paths.enums', 'Enums');
                break;
            default:
                $path = '';
        }
        if ($directoryPath) {
            $path = str_replace('\\', '/', $path);
        } else {
            $path = str_replace('/', '\\', $path);
        }
        return $path;
    }

    abstract public function getPathConfigNode();

    public function getNamespace()
    {
        $segments = $this->getSegments();
        array_pop($segments);
        $rootNamespace = $this->getRootNamespace();
        if ($rootNamespace == false) {
            return null;
        }

        return rtrim($rootNamespace . '\\' . implode('\\', $segments), '\\') . ';';
    }

    public function run()
    {
        if ($this->filesystem->exists($path = $this->getPath()) && !$this->force) {
            throw new FileAlreadyExistsException($path);
        }
        if (!$this->filesystem->isDirectory($dir = dirname($path))) {
            $this->filesystem->makeDirectory($dir, 0777, true, true);
        }

        return $this->filesystem->put($path, $this->getStub());
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function hasOption($key)
    {
        return array_key_exists($key, $this->options);
    }

    public function getOption($key, $default = null)
    {
        if (!$this->hasOption($key)) {
            return $default;
        }

        return $this->options[$key] ?: $default;
    }

    public function option($key, $default = null)
    {
        return $this->getOption($key, $default);
    }

    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }

        return $this->option($key);
    }


}
