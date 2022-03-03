<?php

namespace HHH\Tests\Unit\Command\Quality\Config;

use HHH\Command\Quality\Config\PhpUnitServiceConfig;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \HHH\Command\Quality\Config\PhpUnitServiceConfig */
class PhpUnitServiceConfigTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getExecutableFile
     * @covers ::getConfigurationFile
     * @covers ::getReportTarget
     * @covers ::getReportFormat
     */
    public function testConstruct(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportTarget = '__dummy_report_target__';
        $reportFormat = '__dummy_report_format__';
        $sut = new PhpUnitServiceConfig($execFile, $configFile, $reportTarget, $reportFormat);

        $this->assertEquals($execFile, $sut->getExecutableFile());
        $this->assertEquals($configFile, $sut->getConfigurationFile());
        $this->assertEquals($reportTarget, $sut->getReportTarget());
        $this->assertEquals($reportFormat, $sut->getReportFormat());
    }
}
