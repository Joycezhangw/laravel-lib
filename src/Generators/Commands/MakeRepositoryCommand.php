<?php


namespace JoyceZ\LaravelLib\Generators\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use JoyceZ\LaravelLib\Exceptions\FileAlreadyExistsException;
use JoyceZ\LaravelLib\Generators\ModelGenerator;
use JoyceZ\LaravelLib\Generators\RepositoryGenerator;
use JoyceZ\LaravelLib\Generators\RepositoryInterfaceGenerator;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Artisan 命令生成 Repository 相关文件
 * Class MakeRepositoryCommand
 * @package JoyceZ\LaravelLib\Generators\Commands
 */
class MakeRepositoryCommand extends Command
{

    /**
     * 命令名称
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * 命令说明
     * @var string
     */
    protected $description = 'Create a new repository class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */

    protected $type = 'Repository';

    protected $generators = null;

    public function handle()
    {
        $this->laravel->call([$this, 'fire'], func_get_args());
    }

    public function fire()
    {
        $this->generators = new Collection();

        $modelGenerator = new ModelGenerator([
            'name' => $this->argument('name'),
        ]);

        $repositoryInterfaceGenerator = new RepositoryInterfaceGenerator([
            'name' => $this->argument('name'),
        ]);
        //model use
        $model = $modelGenerator->getRootNamespace() . '\\' . $modelGenerator->getName();
        $model = str_replace([
            "\\",
            '/'
        ], '\\', $model . 'Model');
        //interfaces use
        $interfaces = $repositoryInterfaceGenerator->getRootNamespace() . '\\' . implode('/', $repositoryInterfaceGenerator->resetSegments());
        $interfaces = str_replace([
            "\\",
            '/'
        ], '\\', $interfaces);
        try {
            $modelGenerator->run();
            $this->info("Model created successfully.");
            $repositoryInterfaceGenerator->run();
            $this->info("Repository Interface created successfully.");
            (new RepositoryGenerator([
                'name' => $this->argument('name'),
                'interfaces' => $interfaces,
                'model' => $model
            ]))->run();
            $this->info("Repository created successfully.");
        } catch (FileAlreadyExistsException $exception) {
            $this->error($this->type . ' already exists!');
            return false;
        }
    }

    public function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The name of class being generated.',
                null
            ],
        ];
    }

    public function getOptions()
    {
        return [];
    }


}
