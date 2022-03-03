<?php

namespace Hippy\Tests\Unit\Command\Quality\Config;

use Hippy\Command\Quality\Config\PhpCodeBeautifierServiceConfig;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Command\Quality\Config\PhpCodeBeautifierServiceConfig */
class PhpCodeBeautifierServiceConfigTest extends TestCase
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
        $sut = new PhpCodeBeautifierServiceConfig($execFile, $configFile);

        $this->assertEquals($execFile, $sut->getExecutableFile());
        $this->assertEquals($configFile, $sut->getConfigurationFile());
    }
}
