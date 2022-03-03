<?php

namespace Hippy\Command\Quality\Service;

use Hippy\Command\CommandException;

abstract class AbstractService
{
    /**
     * @param string $executableFile
     * @return void
     * @throws CommandException
     */
    protected function checkExecutableFile(string $executableFile): void
    {
        if (!$this->fileExists($executableFile)) {
            throw new CommandException('Executable not found', 'error');
        }
    }

    /**
     * @param string $configurationFile
     * @return void
     * @throws CommandException
     */
    protected function checkConfigurationFile(string $configurationFile): void
    {
        if (!$this->fileExists($configurationFile)) {
            throw new CommandException('Configuration file not found', 'error');
        }
    }

    /**
     * @param string $reportDir
     * @return void
     * @throws CommandException
     */
    protected function checkReportDir(string $reportDir): void
    {
        // make sure that the output directory is present
        $command = 'mkdir -p ' . $reportDir;
        $output = [];
        $returnVal = 0;
        $this->exec($command, $output, $returnVal);
        if (0 != $returnVal) {
            throw new CommandException('Error while creating report output dir : ' . implode($output), 'error');
        }
    }

    /**
     * @param string $file
     * @return bool
     * @codeCoverageIgnore
     */
    protected function fileExists(string $file): bool
    {
        return file_exists($file);
    }

    /**
     * @param string $command
     * @param string[] $output
     * @param int|null $returnVar
     * @return false|string
     * @codeCoverageIgnore
     */
    protected function exec(
        string $command,
        array &$output = null,
        int &$returnVar = null,
    ): false|string {
        return exec($command, $output, $returnVar);
    }

    /**
     * @param string $command
     * @param null|int $returnVar
     * @return false|string
     * @codeCoverageIgnore
     */
    protected function system(
        string $command,
        int &$returnVar = null,
    ): false|string {
        return system($command, $returnVar);
    }
}
