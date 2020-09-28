<?php

namespace Huztw\Permission\Console;

use Illuminate\Console\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * Get Install path.
     *
     * @param $path
     *
     * @return string
     */
    protected function installPath($path = null)
    {
        $directory = trim(config('admin.directory'), '/');

        $installPath = ($path === null) ? $directory : "$directory/$path";

        return str_replace('/', DIRECTORY_SEPARATOR, $installPath);
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__ . "/stubs/$name.stub");
    }

    /**
     * Make new directory.
     *
     * @param string $directory
     */
    protected function makeDir($directory)
    {
        $this->laravel['files']->makeDirectory($directory, 0755, true, true);

        $this->line('<info>Created directory: </info> ' . preg_replace('/\/|\\\\/', DIRECTORY_SEPARATOR, str_replace(base_path(), '', $directory)));
    }

    /**
     * Make new file.
     *
     * @param string $file
     * @param string $contents
     */
    protected function makefile($file, $contents)
    {
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));

        $this->line('<info>Created file: </info> ' . preg_replace('/\/|\\\\/', DIRECTORY_SEPARATOR, str_replace(base_path(), '', $file)));
    }

    /**
     * Delete directory.
     *
     * @param string $directory
     */
    protected function deleteDir($directory)
    {
        $this->laravel['files']->deleteDirectory($directory);

        $this->line('<error>Deleted directory: </error> ' . preg_replace('/\/|\\\\/', DIRECTORY_SEPARATOR, str_replace(base_path(), '', $directory)));
    }

    /**
     * Delete file.
     *
     * @param string $file
     */
    protected function deleteFile($file)
    {
        $this->laravel['files']->delete($file);

        $this->line('<error>Deleted file: </error> ' . preg_replace('/\/|\\\\/', DIRECTORY_SEPARATOR, str_replace(base_path(), '', $file)));
    }

    /**
     * Delete file.
     *
     * @return array
     */
    protected function getMigrations()
    {
        $migrationsDir = __DIR__ . '/../../database/migrations';

        $migrations = [];

        foreach (scandir($migrationsDir) as $file) {
            if ('.' != $file && '..' != $file) {
                array_push($migrations, $file);
            }
        }

        return $migrations;
    }
}
