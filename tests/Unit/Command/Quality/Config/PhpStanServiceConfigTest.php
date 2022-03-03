<?php

namespace HHH\Tests\Unit\Command\Quality\Config;

use HHH\Command\Quality\Config\PhpStanServiceConfig;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \HHH\Command\Quality\Config\PhpStanServiceConfig */
class PhpStanServiceConfigTest extends TestCase
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
        $sut = new PhpStanServiceConfig($execFile, $configFile);

        $this->assertEquals($execFile, $sut->getExecutableFile());
        $this->assertEquals($configFile, $sut->getConfigurationFile());
    }
}
