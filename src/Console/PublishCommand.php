<?php

namespace Huztw\Permission\Console;

use Huztw\Permission\PermissionServiceProvider;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'huztw:permission:publish {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "re-publish Huztw Permission. If you want overwrite the existing files, you can add the `--force` option";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = ['--provider' => PermissionServiceProvider::class];

        if ($this->option('force') == true) {
            $options['--force'] = true;
        }

        $this->call('vendor:publish', $options);
    }
}
