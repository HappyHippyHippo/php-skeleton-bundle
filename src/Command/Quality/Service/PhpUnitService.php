<?php

namespace Hippy\Command\Quality\Service;

use Hippy\Command\CommandException;
use Hippy\Command\Quality\Config\PhpUnitServiceConfig;
use Hippy\Config\Config;
use Symfony\Component\Console\Command\Command;

class PhpUnitService extends AbstractService
{
    /** @var string[] */
    protected const ALLOWED_FORMATS = ['html', 'text', 'xml'];

    /**
     * @param Config $config
     */
    public function __construct(protected Config $config)
    {
    }

    /**
     * @param PhpUnitServiceConfig $config
     * @return int
     * @throws CommandException
     */
    public function execute(PhpUnitServiceConfig $config): int
    {
        $executableFile = $this->config->getRoot() . $config->getExecutableFile();
        $configurationFile = $this->config->getRoot() . $config->getConfigurationFile();
        $reportTarget = $this->config->getRoot() . $config->getReportTarget();
        $reportFormat = $config->getReportFormat();

        $this->checkExecutableFile($executableFile);
        $this->checkConfigurationFile($configurationFile);
        $this->checkReportFormat($reportFormat);

        // executes the phpcs app
        $command = sprintf('%s -c %s', $executableFile, $configurationFile);

        switch ($reportFormat) {
            case 'text':
                $command .= ' --coverage-text';
                break;
            case 'xml':
                $this->checkReportDir($reportTarget);
                $command .= sprintf(' --coverage-xml=%s', $reportTarget);
                break;
            case 'html':
                $this->checkReportDir($reportTarget);
                $command .= sprintf(' --coverage-html=%s', $reportTarget);
                break;
        }

        $return = 0;
        $this->system($command, $return);
        if (0 != $return) {
            throw new CommandException('Errors/warnings have been found', 'warning');
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $reportFormat
     * @return void
     * @throws CommandException
     */
    protected function checkReportFormat(string $reportFormat): void
    {
        if (!in_array($reportFormat, self::ALLOWED_FORMATS)) {
            throw new CommandException('Unsupported output format', 'error');
        }
    }
}
