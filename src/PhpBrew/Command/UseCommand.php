<?php

namespace PhpBrew\Command;

use CLIFramework\Command;
use PhpBrew\Config;

/**
 * @codeCoverageIgnore
 */
class UseCommand extends Command
{
    public function arguments($args)
    {
        $args->add('php version')
            ->validValues(function () {
                return \PhpBrew\BuildFinder::findInstalledBuilds();
            })
            ;
    }

    public function brief()
    {
        return 'Use php, switch version temporarily';
    }

    public function execute($buildName)
    {
        if (!$buildName) {
            // This exception is used for tracing tests
            throw new \Exception('build name is required.');
        }
        { // this block is important for tests only
            $root = Config::getRoot();
            $home = Config::getHome();
            if (!file_exists("$root/php/$buildName")) {
                throw new \Exception("build $buildName doesn't exist.");
            }
            putenv("PHPBREW_ROOT=$root");
            putenv("PHPBREW_HOME=$home");
            putenv("PHPBREW_PHP=$buildName") or die('putenv failed');
            putenv("PHPBREW_PATH=$root/php/$buildName/bin");
            putenv("PHPBREW_BIN=$home/bin");
        }
        if (!getenv('TRAVIS')) {
            $this->logger->warning("You should not see this, if you see this, it means you didn't load the ~/.phpbrew/bashrc script, please check if bashrc is sourced in your shell.");
        }
    }
}
