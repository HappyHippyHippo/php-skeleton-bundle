<?php

namespace HHH\Command\Quality\Config;

class PhpCodeSnifferServiceConfig
{
    /**
     * @param string $executableFile
     * @param string $configurationFile
     * @param string $reportType
     * @param string $reportDir
     * @param string $reportFile
     */
    public function __construct(
        protected string $executableFile,
        protected string $configurationFile,
        protected string $reportType,
        protected string $reportDir,
        protected string $reportFile,
    ) {
    }

    /**
     * @return string
     */
    public function getExecutableFile(): string
    {
        return $this->executableFile;
    }

    /**
     * @return string
     */
    public function getConfigurationFile(): string
    {
        return $this->configurationFile;
    }

    /**
     * @return string
     */
    public function getReportType(): string
    {
        return $this->reportType;
    }

    /**
     * @return string
     */
    public function getReportDir(): string
    {
        return $this->reportDir;
    }

    /**
     * @return string
     */
    public function getReportFile(): string
    {
        return $this->reportFile;
    }
}
