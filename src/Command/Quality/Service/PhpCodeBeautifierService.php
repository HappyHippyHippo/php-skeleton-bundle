<?php

namespace Hippy\Command\Quality\Service;

use Hippy\Command\CommandException;
use Hippy\Command\Quality\Config\PhpCodeBeautifierServiceConfig;
use Hippy\Config\ConfigInterface;
use Symfony\Component\Console\Command\Command;

class PhpCodeBeautifierService extends AbstractService
{
    /**
     * @param ConfigInterface $config
     */
    public function __construct(protected ConfigInterface $config)
    {
    }

    /**
     * @param PhpCodeBeautifierServiceConfig $config
     * @return int
     * @throws CommandException
     */
    public function execute(PhpCodeBeautifierServiceConfig $config): int
    {
        $executableFile = $this->config->getRoot() . $config->getExecutableFile();
        $configurationFile = $this->config->getRoot() . $config->getConfigurationFile();

        $this->checkExecutableFile($executableFile);
        $this->checkConfigurationFile($configurationFile);

        // executes the phpcs app
        $command = sprintf(
            '%s -p --standard=%s',
            $executableFile,
            $configurationFile
        );

        $return = 0;
        $this->system($command, $return);
        if (0 != $return) {
            throw new CommandException('Errors/warnings have been found', 'warning');
        }

        return Command::SUCCESS;
    }
}
