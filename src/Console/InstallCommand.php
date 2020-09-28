<?php

namespace Huztw\Admin\Console;

use Huztw\Admin\Database\Seeds\AdminSeeder;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the admin package';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->initDatabase();

        $this->initAdminDirectory();
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');

        $userModel = config('admin.database.users_model');

        if ($userModel::count() == 0) {
            $this->call('db:seed', ['--class' => AdminSeeder::class]);
        }
    }

    /**
     * Initialize the admAin directory.
     *
     * @return void
     */
    protected function initAdminDirectory()
    {
        if (is_dir($this->installPath())) {
            $this->line('<error>' . $this->installPath() . ' directory already exists !</error> ');

            return;
        }

        $this->makeDir($this->installPath());

        $this->makeDir($this->installPath('Controllers'));

        $this->createRoutesFile();

        $this->createBootstrapFile();

        $this->createControllers();

        $this->line('<info>Installing huztw-admin!</info>');
    }

    /**
     * Create Controllers.
     *
     * @return void
     */
    public function createControllers()
    {
        $homeController = $this->installPath('Controllers/HomeController.php');
        $this->makefile($homeController, $this->getStub('HomeController'));

        $loginController = $this->installPath('Controllers/LoginController.php');
        $this->makefile($loginController, $this->getStub('LoginController'));

        $exampleController = $this->installPath('Controllers/ExampleController.php');
        $this->makefile($exampleController, $this->getStub('ExampleController'));
    }

    /**
     * Create bootstrap file.
     *
     * @return void
     */
    protected function createBootstrapFile()
    {
        $bootstrap = $this->installPath('bootstrap.php');
        $this->makefile($bootstrap, $this->getStub('bootstrap'));
    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createRoutesFile()
    {
        $routes = $this->installPath('routes.php');
        $this->makefile($routes, $this->getStub('routes'));

        $push = $this->installPath('push.php');
        $this->makefile($push, $this->getStub('push'));
    }
}
