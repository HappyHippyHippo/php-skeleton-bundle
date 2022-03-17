<?php

namespace Hippy\Command\Quality\Service;

use Hippy\Command\CommandException;
use Hippy\Command\Quality\Config\PhpCodeSnifferServiceConfig;
use Hippy\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhpCodeSnifferService extends AbstractService
{
    /**
     * @param Config $config
     */
    public function __construct(protected Config $config)
    {
    }

    /**
     * @param PhpCodeSnifferServiceConfig $config
     * @param SymfonyStyle $styler
     * @return int
     * @throws CommandException
     */
    public function execute(PhpCodeSnifferServiceConfig $config, SymfonyStyle $styler): int
    {
        $executableFile = $this->config->getRoot() . $config->getExecutableFile();
        $configurationFile = $this->config->getRoot() . $config->getConfigurationFile();

        $this->checkExecutableFile($executableFile);
        $this->checkConfigurationFile($configurationFile);

        // executes the phpcs app
        $command = sprintf(
            '%s -s -p --standard=%s  --report=%s',
            $executableFile,
            $configurationFile,
            $config->getReportType()
        );

        $reportFile = $config->getReportFile();
        if (!empty($reportFile)) {
            $reportDir = $this->config->getRoot() . $config->getReportDir();
            $this->checkReportDir($reportDir);

            $reportFile = $reportDir . $reportFile;
            $command .= sprintf(' --report-file=%s', $reportFile);
        }

        $return = 0;
        $this->system($command, $return);
        if (0 != $return) {
            $writer = function () use ($styler, $reportFile) {
                $styler->writeln((string) $this->getFileContents($reportFile));
            };
            throw new CommandException('Errors/warnings have been found', 'warning', $writer);
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $file
     * @return false|string
     * @codeCoverageIgnore
     */
    protected function getFileContents(string $file): false|string
    {
        return file_get_contents($file);
    }
}
