<?php

namespace Displore\Core\Installer;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'displore:install
                            {package : The Displore package to install or a path to a config file.}
                            {--dev : Install the dev-master branch}
                            {--config : Treat the name as a path to a config file.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a (Displore) package.';

    /**
     * The composer instance.
     * 
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new InstallCommand instance.
     * @param \Illuminate\Support\Composer $composer
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('config')) {
            $this->installFromConfig($this->argument('package'));
        } else {
            $this->installSinglePackage(strtolower($this->argument('package')));
        }
    }

    /**
     * Install a single Displore package.
     * 
     * @param  string $package
     * @return void
     */
    public function installSinglePackage($package)
    {
        // Check if we should give composer the `--dev` flag.
        if ($this->option('dev')) {
            // Execute composer.
            $this->composer->requireDevDependency('displore/'.$package);

            // if composer failed, show the errors.
            if ($this->composer->composerErrors) {
                $this->error($this->composer->composerErrors);
                exit();
            }

            // Do some magic with Laravel (see below).
            $this->SetLaravelLogic('displore/'.$package);

            // Dump the autoloads and you're good to go!
            $this->composer->dumpAutoloads();
            $this->info('Successfully installed displore/'.$package);
        } else {
            // Execute composer.
            $this->composer->requireDependency('displore/'.$package);

            // if composer failed, show the errors.
            if ($this->composer->composerErrors) {
                $this->error($this->composer->composerErrors);
                exit();
            }

            // Do some magic with Laravel (see below).
            $this->SetLaravelLogic('displore/'.$package);

            // Dump the autoloads and you're good to go!
            $this->composer->dumpAutoloads();
            $this->info('Successfully installed displore/'.$package);
        }
    }

    /**
     * Install one or more packages, based on a JSON config file.
     * The config file is searched for starting on the base_path().
     *
     * @param  string $file
     * @return void
     */
    public function InstallFromConfig($file)
    {
        // If the given file has no json extension, add it.
        if (pathinfo($file, PATHINFO_EXTENSION) != 'json') {
            $file = $file.'.json';
        }

        // Get the absolute location of the file.
        $path = realpath(base_path($file));

        // If there is no such file, abort the mission.
        if ( ! file_exists($path)) {
            $this->error('There is no configuration found!');
            $this->info('I looked for it in '.base_path().'/'.$file);
            $this->info('Please try again.');
            exit();
        }

        // Or else continue with the journey.
        $config = json_decode(file_get_contents($path), true);

        // If there is a requirement, install the single package.
        if (isset($config['requirement'])) {
            $package = $config;
            $this->info('Installing '.$package['name'].'...');
            $this->installSinglePackage($package['requirement']);
            $this->SetLaravelLogic($package);
            $this->info('Successfully installed '.$package['requirement']);
        }

        // If there is a packages array, install all of them.
        if (isset($config['packages'])) {
            $this->info('Installing a list of packages...');

            foreach ($config['packages'] as $package) {
                $this->info('Installing '.$package['name'].'...');
                $this->installSinglePackage($package['requirement']);
                $this->SetLaravelLogic($package);
                $this->info('Successfully installed '.$package['requirement']);
            }
        }
    }

    /**
     * Setting all of the Laravel logic, such as adding the service provider
     * and Facade to the `config/app.php` arrays.
     * 
     * @param  array $package
     * @return void
     */
    protected function setLaravelLogic($package)
    {
        // Find the package.
        $path = base_path('vendor/'.$package['requirement']);

        // Look for a configuration file.
        if ( ! file_exists($path.'/displore.json')) {
            $this->error('There is no displore.json found!');
            $this->info('I looked for it in '.$path);
            $this->info('You should add the Laravel logic (service provider, facade) yourself (sorry!)');
            exit();
        }

        // There should be a `provides` array.
        if ( ! isset($package['provides'])) {
            $this->error('There are no provides defined.');
            $this->info('No service provider or facade will be added.');

            return;
        }

        // Adding service provider.
        if (isset($package['provides']['serviceprovider'])) {
            $appConfigLine = 'Displore\Core\CoreServiceProvider::class,
            '.$package['provides']['serviceprovider'].'::class,';

            $file = file_get_contents(base_path('config/app.php'));
            $newFile = str_replace(
                'Displore\Core\CoreServiceProvider::class',
                $appConfigLine,
                $file
            );
            file_put_contents(base_path('config/app.php'), $newFile);
        }

        // Adding facade.
        if (isset($package['provides']['facade'])) {
            $appConfigLine = '\'View\' => Illuminate\Support\Facades\View::class,

            '.$package['provides']['facade'].' => '.$package['provides']['facade-namespace'].'::class,';

            $file = file_get_contents(base_path('config/app.php'));
            $newFile = str_replace(
                '\'View\' => Illuminate\Support\Facades\View::class,',
                $appConfigLine,
                $file
            );
            file_put_contents(base_path('config/app.php'), $newFile);
        }
    }
}
