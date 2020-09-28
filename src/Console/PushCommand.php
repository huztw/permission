<?php

namespace Huztw\Admin\Console;

use Huztw\Admin\Database\Seeds\ActionSeeder;
use Huztw\Admin\Database\Seeds\AssetSeeder;
use Huztw\Admin\Database\Seeds\BladeSeeder;
use Huztw\Admin\Database\Seeds\PermissionSeeder;
use Huztw\Admin\Database\Seeds\RouteSeeder;
use Huztw\Admin\Database\Seeds\ViewSeeder;

class PushCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push the admin setting';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('<info>Pushing table</info>');

        $this->line('Route table :');

        $this->call('db:seed', ['--class' => RouteSeeder::class]);

        $this->line('Permission table :');

        $this->call('db:seed', ['--class' => PermissionSeeder::class]);

        $this->line('Action table :');

        $this->call('db:seed', ['--class' => ActionSeeder::class]);

        $this->line('View table :');

        $this->call('db:seed', ['--class' => ViewSeeder::class]);

        $this->line('Blade table :');

        $this->call('db:seed', ['--class' => BladeSeeder::class]);

        $this->line('Asset table :');

        $this->call('db:seed', ['--class' => AssetSeeder::class]);

        $this->line('<info>Pushing complete</info>');
    }
}
