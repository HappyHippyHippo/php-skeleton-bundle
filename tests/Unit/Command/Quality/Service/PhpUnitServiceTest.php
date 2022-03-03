<?php

namespace HHH\Tests\Unit\Command\Quality\Service;

use HHH\Command\CommandException;
use HHH\Command\Quality\Config\PhpUnitServiceConfig;
use HHH\Command\Quality\Service\PhpUnitService;
use HHH\Config\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command;

/** @coversDefaultClass \HHH\Command\Quality\Service\PhpUnitService */
class PhpUnitServiceTest extends TestCase
{
    /** @var string */
    protected const ROOT = '__dummy_root__';

    /** @var ConfigInterface&MockObject */
    protected ConfigInterface $config;

    /** @var PhpUnitServiceConfig&MockObject */
    protected PhpUnitServiceConfig $phpunitConfig;

    /** @var PhpUnitService&MockObject */
    protected PhpUnitService $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->phpunitConfig = $this->createMock(PhpUnitServiceConfig::class);
        $this->sut = $this->getMockBuilder(PhpUnitService::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['checkExecutableFile', 'checkConfigurationFile', 'checkReportDir', 'system'])
            ->getMock();
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $property = new ReflectionProperty(PhpUnitService::class, 'config');
        $this->assertSame($this->config, $property->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::execute
     * @covers ::checkReportFormat
     * @throws CommandException
     */
    public function testExecuteThrowsOnUnsupportedFormat(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportTarget = '__dummy_report_target__';
        $reportFormat = 'unsupported';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpunitConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpunitConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);
        $this->phpunitConfig->expects($this->once())->method('getReportTarget')->willReturn($reportTarget);
        $this->phpunitConfig->expects($this->once())->method('getReportFormat')->willReturn($reportFormat);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->never())->method('system');

        $this->expectExceptionObject(new CommandException('Unsupported output format', 'error'));
        $this->sut->execute($this->phpunitConfig);
    }

    /**
     * @return void
     * @covers ::execute
     * @covers ::checkReportFormat
     * @throws CommandException
     */
    public function testExecuteThrowsOnError(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportTarget = '__dummy_report_target__';
        $reportFormat = 'html';
        $return = '__dummy_return__';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpunitConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpunitConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);
        $this->phpunitConfig->expects($this->once())->method('getReportTarget')->willReturn($reportTarget);
        $this->phpunitConfig->expects($this->once())->method('getReportFormat')->willReturn($reportFormat);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('checkReportDir')->with(self::ROOT . $reportTarget);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s -c %s --coverage-%s=%s',
                self::ROOT . $execFile,
                self::ROOT . $configFile,
                $reportFormat,
                self::ROOT . $reportTarget
            ),
            0
        )->willReturnCallback(function (string $command, int &$inReturn) use ($return) {
            return $inReturn = $return;
        });

        $this->expectExceptionObject(new CommandException('Errors/warnings have been found', 'warning'));
        $this->sut->execute($this->phpunitConfig);
    }

    /**
     * @return void
     * @covers ::execute
     * @covers ::checkReportFormat
     * @throws CommandException
     */
    public function testExecuteHtml(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportTarget = '__dummy_report_target__';
        $reportFormat = 'html';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpunitConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpunitConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);
        $this->phpunitConfig->expects($this->once())->method('getReportTarget')->willReturn($reportTarget);
        $this->phpunitConfig->expects($this->once())->method('getReportFormat')->willReturn($reportFormat);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('checkReportDir')->with(self::ROOT . $reportTarget);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s -c %s --coverage-%s=%s',
                self::ROOT . $execFile,
                self::ROOT . $configFile,
                $reportFormat,
                self::ROOT . $reportTarget
            ),
            0
        );

        $this->assertEquals(Command::SUCCESS, $this->sut->execute($this->phpunitConfig));
    }

    /**
     * @return void
     * @covers ::execute
     * @covers ::checkReportFormat
     * @throws CommandException
     */
    public function testExecuteXml(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportTarget = '__dummy_report_target__';
        $reportFormat = 'xml';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpunitConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpunitConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);
        $this->phpunitConfig->expects($this->once())->method('getReportTarget')->willReturn($reportTarget);
        $this->phpunitConfig->expects($this->once())->method('getReportFormat')->willReturn($reportFormat);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('checkReportDir')->with(self::ROOT . $reportTarget);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s -c %s --coverage-%s=%s',
                self::ROOT . $execFile,
                self::ROOT . $configFile,
                $reportFormat,
                self::ROOT . $reportTarget
            ),
            0
        );

        $this->assertEquals(Command::SUCCESS, $this->sut->execute($this->phpunitConfig));
    }

    /**
     * @return void
     * @covers ::execute
     * @covers ::checkReportFormat
     * @throws CommandException
     */
    public function testExecuteText(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportTarget = '__dummy_report_target__';
        $reportFormat = 'text';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpunitConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpunitConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);
        $this->phpunitConfig->expects($this->once())->method('getReportTarget')->willReturn($reportTarget);
        $this->phpunitConfig->expects($this->once())->method('getReportFormat')->willReturn($reportFormat);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->never())->method('checkReportDir');
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s -c %s --coverage-%s',
                self::ROOT . $execFile,
                self::ROOT . $configFile,
                $reportFormat
            ),
            0
        );

        $this->assertEquals(Command::SUCCESS, $this->sut->execute($this->phpunitConfig));
    }
}
