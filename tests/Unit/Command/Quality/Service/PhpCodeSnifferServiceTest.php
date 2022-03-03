<?php

namespace HHH\Tests\Unit\Command\Quality\Service;

use HHH\Command\CommandException;
use HHH\Command\Quality\Config\PhpCodeSnifferServiceConfig;
use HHH\Command\Quality\Service\PhpCodeSnifferService;
use HHH\Config\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \HHH\Command\Quality\Service\PhpCodeSnifferService */
class PhpCodeSnifferServiceTest extends TestCase
{
    /** @var string */
    protected const ROOT = '__dummy_root__';

    /** @var ConfigInterface&MockObject */
    protected ConfigInterface $config;

    /** @var PhpCodeSnifferServiceConfig&MockObject */
    protected PhpCodeSnifferServiceConfig $phpcsConfig;

    /** @var SymfonyStyle&MockObject */
    protected SymfonyStyle $styler;

    /** @var PhpCodeSnifferService&MockObject */
    protected PhpCodeSnifferService $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->phpcsConfig = $this->createMock(PhpCodeSnifferServiceConfig::class);
        $this->styler = $this->createMock(SymfonyStyle::class);
        $this->sut = $this->getMockBuilder(PhpCodeSnifferService::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods([
                'checkExecutableFile',
                'checkConfigurationFile',
                'checkReportDir',
                'system',
                'getFileContents',
            ])
            ->getMock();
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $property = new ReflectionProperty(PhpCodeSnifferService::class, 'config');
        $this->assertSame($this->config, $property->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::execute
     */
    public function testExecuteThrowsOnError(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportType = '__dummy_report_type__';
        $reportDir = '__dummy_report_dir__';
        $reportFile = '__dummy_report_file__';
        $reportContent = '__dummy_report_content__';
        $return = '__dummy_return__';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpcsConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpcsConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);
        $this->phpcsConfig->expects($this->once())->method('getReportType')->willReturn($reportType);
        $this->phpcsConfig->expects($this->once())->method('getReportDir')->willReturn($reportDir);
        $this->phpcsConfig->expects($this->once())->method('getReportFile')->willReturn($reportFile);

        $this->styler
            ->expects($this->once())
            ->method('writeln')
            ->with($reportContent);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('checkReportDir')->with(self::ROOT . $reportDir);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s -s -p --standard=%s  --report=%s --report-file=%s',
                self::ROOT . $execFile,
                self::ROOT . $configFile,
                $reportType,
                self::ROOT . $reportDir . $reportFile
            ),
            0
        )->willReturnCallback(function (string $command, int &$inReturn) use ($return) {
            return $inReturn = $return;
        });
        $this->sut
            ->expects($this->once())
            ->method('getFileContents')
            ->with(self::ROOT . $reportDir . $reportFile)
            ->willReturn($reportContent);

        try {
            $this->sut->execute($this->phpcsConfig, $this->styler);
        } catch (CommandException $exception) {
            $this->assertEquals('Errors/warnings have been found', $exception->getMessage());
            $this->assertEquals('warning', $exception->getMessageType());
            $this->assertNull($exception->getPostMessageAction());

            $callback = $exception->getPreMessageAction();
            $this->assertNotNull($callback);

            if (is_callable($callback)) {
                $callback();
            }

            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::execute
     * @throws CommandException
     */
    public function testExecute(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $reportType = '__dummy_report_type__';
        $reportDir = '__dummy_report_dir__';
        $reportFile = '__dummy_report_file__';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpcsConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpcsConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);
        $this->phpcsConfig->expects($this->once())->method('getReportType')->willReturn($reportType);
        $this->phpcsConfig->expects($this->once())->method('getReportDir')->willReturn($reportDir);
        $this->phpcsConfig->expects($this->once())->method('getReportFile')->willReturn($reportFile);
        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('checkReportDir')->with(self::ROOT . $reportDir);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s -s -p --standard=%s  --report=%s --report-file=%s',
                self::ROOT . $execFile,
                self::ROOT . $configFile,
                $reportType,
                self::ROOT . $reportDir . $reportFile
            ),
            0
        );

        $this->assertEquals(Command::SUCCESS, $this->sut->execute($this->phpcsConfig, $this->styler));
    }
}
