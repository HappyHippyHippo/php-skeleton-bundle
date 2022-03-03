<?php

namespace HHH\Tests\Unit\Command\Quality\Config;

use HHH\Command\Quality\Config\PhpCodeStandardFixerServiceConfig;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \HHH\Command\Quality\Config\PhpCodeStandardFixerServiceConfig */
class PhpCodeStandardFixerServiceConfigTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getExecutableFile
     * @covers ::getConfigurationFile
     */
    public function testConstruct(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $sut = new PhpCodeStandardFixerServiceConfig($execFile, $configFile);

        $this->assertEquals($execFile, $sut->getExecutableFile());
        $this->assertEquals($configFile, $sut->getConfigurationFile());
    }
}
