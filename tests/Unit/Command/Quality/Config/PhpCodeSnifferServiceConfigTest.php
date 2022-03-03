<?php

namespace HHH\Tests\Unit\Command\Quality\Config;

use HHH\Command\Quality\Config\PhpCodeSnifferServiceConfig;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \HHH\Command\Quality\Config\PhpCodeSnifferServiceConfig */
class PhpCodeSnifferServiceConfigTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getExecutableFile
     * @covers ::getConfigurationFile
     * @covers ::getReportType
     * @covers ::getReportDir
     * @covers ::getReportFile
     */
    public function testConstruct(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportType = '__dummy_report_type__';
        $reportDir = '__dummy_report_dir__';
        $reportFile = '__dummy_report_file__';
        $sut = new PhpCodeSnifferServiceConfig($execFile, $configFile, $reportType, $reportDir, $reportFile);

        $this->assertEquals($execFile, $sut->getExecutableFile());
        $this->assertEquals($configFile, $sut->getConfigurationFile());
        $this->assertEquals($reportType, $sut->getReportType());
        $this->assertEquals($reportDir, $sut->getReportDir());
        $this->assertEquals($reportFile, $sut->getReportFile());
    }
}
