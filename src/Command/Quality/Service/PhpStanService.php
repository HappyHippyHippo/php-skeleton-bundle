<?php

namespace Hippy\Command\Quality\Service;

use Hippy\Command\CommandException;
use Hippy\Command\Quality\Config\PhpStanServiceConfig;
use Hippy\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhpStanService extends AbstractService
{
    /**
     * @param Config $config
     */
    public function __construct(protected Config $config)
    {
    }

    /**
     * @param PhpStanServiceConfig $config
     * @param SymfonyStyle $styler
     * @return int
     * @throws CommandException
     */
    public function execute(PhpStanServiceConfig $config, SymfonyStyle $styler): int
    {
        $executableFile = $this->config->getRoot() . $config->getExecutableFile();
        $configurationFile = $this->config->getRoot() . $config->getConfigurationFile();

        $this->checkExecutableFile($executableFile);
        $this->checkConfigurationFile($configurationFile);

        // executes the phpstan app
        $command = sprintf(
            '%s analyse -c "%s"',
            $executableFile,
            $configurationFile
        );

        $styler->writeln('command : ' . $command);

        $return = 0;
        $message = $this->system($command, $return);
        if (0 != $return) {
            $writer = function () use ($styler, $message) {
                $styler->writeln((string) $message);
            };
            throw new CommandException('Errors/warnings have been found', 'warning', $writer);
        }

        return Command::SUCCESS;
    }
}
