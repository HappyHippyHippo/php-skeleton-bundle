<?php

namespace HHH\Command\Quality\Config;

class PhpCodeStandardFixerServiceConfig
{
    /**
     * @param string $executableFile
     * @param string $configurationFile
     */
    public function __construct(
        protected string $executableFile,
        protected string $configurationFile,
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
}
