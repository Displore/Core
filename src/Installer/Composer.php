<?php

namespace Displore\Core\Installer;

use Illuminate\Support\Composer as IlluminateComposer;

class Composer extends IlluminateComposer
{
    /**
     * Container of possible errors that rose during the process.
     *
     * @var string
     */
    public $composerErrors;

    /**
     * Execute a composer command.
     *
     * @param string $command
     */
    public function executeCommand($command)
    {
        $process = $this->getProcess();

        $process->setCommandLine(trim($this->findComposer().' '.$command));

        $process->run();

        $this->composerErrors = $process->getErrorOutput();
    }

    /**
     * Execute `composer require` with the dependency.
     *
     * @param string $dependency
     */
    public function requireDependency($dependency)
    {
        $this->executeCommand('require '.$dependency);
    }

    /**
     * Execute `composer require --dev` with the dependency.
     *
     * @param string $devDependency
     */
    public function requireDevDependency($devDependency)
    {
        $this->executeCommand('require '.$devDependency.' --dev');
    }
}
