<?php

namespace Huztw\Admin\Console;

class UninstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:uninstall
                            {--force : Uninstall the publish files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall the admin package. If you want uninstall the publish files, you can add the `--force` option';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->confirm('Are you sure to uninstall huztw-admin?')) {
            return;
        }

        $this->removeFilesAndDirectories();

        $this->line('<info>Uninstalling huztw-admin!</info>');
    }

    /**
     * Remove files and directories.
     *
     * @return void
     */
    protected function removeFilesAndDirectories()
    {
        $this->deleteDir(config('admin.directory'));
        $this->deleteDir(public_path('vendor/huztw-admin'));
        $this->deleteFile(config_path('admin.php'));

        if ($this->option('force')) {
            $this->removePublish();
        }
    }

    /**
     * Remove publish files.
     *
     * @return void
     */
    protected function removePublish()
    {
        foreach ($this->getMigrations() as $migration) {
            $this->deleteFile(database_path("migrations/$migration"));
        }

        foreach (scandir(resource_path('lang')) as $lang) {
            if ('.' != $lang && '..' != $lang && !isset(pathinfo($lang)['extension'])) {
                $langfile = "$lang/admin.php";
                $this->deleteFile(resource_path("lang/$langfile"));
            }
        }

        $this->deleteDir(resource_path('views/errors/admin'));

        $this->deleteFile(resource_path('views/layouts/admin.blade.php'));
    }
}
