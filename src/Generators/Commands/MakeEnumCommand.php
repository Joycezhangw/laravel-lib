<?php


namespace JoyceZ\LaravelLib\Generators\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use JoyceZ\LaravelLib\Generators\EnumGenerator;
use JoyceZ\LaravelLib\Exceptions\FileAlreadyExistsException;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Artisan 命令生成 enum 文件
 * Class MakeEnumCommand
 * @package JoyceZ\LaravelLib\Generators\Commands
 */
class MakeEnumCommand extends Command
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'make:enum';

    /**
     * 命令说明
     * @var string
     */
    protected $description = 'Create a new enum class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */

    protected $type = 'Enums';

    protected $generators = null;

    public function handle()
    {
        $this->laravel->call([$this, 'fire'], func_get_args());
    }

    public function fire()
    {
        $this->generators = new Collection();
        try {
            (new EnumGenerator(['name' => $this->argument('name')]))->run();
            $this->info("Enum created successfully.");
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
