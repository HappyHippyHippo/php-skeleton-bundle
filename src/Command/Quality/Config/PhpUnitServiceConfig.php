<?php

namespace HHH\Command\Quality\Config;

class PhpUnitServiceConfig
{
    /**
     * @param string $executableFile
     * @param string $configurationFile
     * @param string $reportTarget
     * @param string $reportFormat
     */
    public function __construct(
        protected string $executableFile,
        protected string $configurationFile,
        protected string $reportTarget,
        protected string $reportFormat,
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
    public function getReportTarget(): string
    {
        return $this->reportTarget;
    }

    /**
     * @return string
     */
    public function getReportFormat(): string
    {
        return $this->reportFormat;
    }
}
