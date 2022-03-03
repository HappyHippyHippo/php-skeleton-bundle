<?php

namespace Hippy\Tests\Unit\Command\Quality;

use Hippy\Command\Quality\Config\PhpCodeSnifferServiceConfig;
use Hippy\Command\Quality\PhpCodeSnifferCommand;
use Hippy\Command\Quality\Service\PhpCodeSnifferService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \Hippy\Command\Quality\PhpCodeSnifferCommand */
class PhpCodeSnifferCommandTest extends TestCase
{
    /** @var PhpCodeSnifferService&MockObject */
    protected PhpCodeSnifferService $service;

    /** @var InputInterface&MockObject */
    protected InputInterface $input;

    /** @var OutputInterface&MockObject */
    protected OutputInterface $output;

    /** @var PhpCodeSnifferCommand&MockObject */
    protected PhpCodeSnifferCommand $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->service = $this->createMock(PhpCodeSnifferService::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->sut = $this->getMockBuilder(PhpCodeSnifferCommand::class)
            ->setConstructorArgs([$this->service])
            ->onlyMethods(['exec'])
            ->getMock();
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $service = new ReflectionProperty(PhpCodeSnifferCommand::class, 'service');
        $this->assertSame($this->service, $service->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::execute
     * @covers ::getOption
     * @throws ReflectionException
     */
    public function testExecute(): void
    {
        $executable = '__dummy_executable__';
        $config = '__dummy_configuration__';
        $reportType = '__dummy_report_type__';
        $reportDir = '__dummy_report_dir__';
        $reportFile = '__dummy_report_file__';

        $input = $this->createMock(InputInterface::class);
        $input
            ->expects($this->exactly(5))
            ->method('getOption')
            ->withConsecutive(['executable'], ['config'], ['report-type'], ['report-dir'], ['report-file'])
            ->willReturnOnConsecutiveCalls($executable, $config, $reportType, $reportDir, $reportFile);
        $output = $this->createMock(OutputInterface::class);

        $styler = $this->createMock(SymfonyStyle::class);
        $styler->expects($this->once())->method('note')->with('Running php code sniffer ...');

        $this->service
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpCodeSnifferServiceConfig $conf) use (
                $executable,
                $config,
                $reportType,
                $reportDir,
                $reportFile
            ) {
                $this->assertEquals($executable, $conf->getExecutableFile());
                $this->assertEquals($config, $conf->getConfigurationFile());
                $this->assertEquals($reportType, $conf->getReportType());
                $this->assertEquals($reportDir, $conf->getReportDir());
                $this->assertEquals($reportFile, $conf->getReportFile());

                return true;
            }));

        $this->sut
            ->expects($this->once())
            ->method('exec')
            ->willReturnCallback(
                function ($inInput, $inOutput, $callback) use ($input, $output, $styler) {
                    $this->assertSame($input, $inInput);
                    $this->assertSame($output, $inOutput);
                    $callback($styler);

                    return 0;
                }
            );

        $method = new ReflectionMethod(PhpCodeSnifferCommand::class, 'execute');
        $method->invoke($this->sut, $input, $output);
    }
}
