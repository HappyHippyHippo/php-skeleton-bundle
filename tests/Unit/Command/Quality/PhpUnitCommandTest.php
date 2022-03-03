<?php

namespace HHH\Tests\Unit\Command\Quality;

use HHH\Command\Quality\Config\PhpUnitServiceConfig;
use HHH\Command\Quality\PhpUnitCommand;
use HHH\Command\Quality\Service\PhpUnitService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \HHH\Command\Quality\PhpUnitCommand */
class PhpUnitCommandTest extends TestCase
{
    /** @var PhpUnitService&MockObject */
    protected PhpUnitService $service;

    /** @var InputInterface&MockObject */
    protected InputInterface $input;

    /** @var OutputInterface&MockObject */
    protected OutputInterface $output;

    /** @var PhpUnitCommand&MockObject */
    protected PhpUnitCommand $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->service = $this->createMock(PhpUnitService::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->sut = $this->getMockBuilder(PhpUnitCommand::class)
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
        $service = new ReflectionProperty(PhpUnitCommand::class, 'service');
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
        $reportTarget = '__dummy_report_target__';
        $reportFormat = '__dummy_report_format__';

        $input = $this->createMock(InputInterface::class);
        $input
            ->expects($this->exactly(4))
            ->method('getOption')
            ->withConsecutive(['executable'], ['config'], ['report-target'], ['report-format'])
            ->willReturnOnConsecutiveCalls($executable, $config, $reportTarget, $reportFormat);
        $output = $this->createMock(OutputInterface::class);

        $styler = $this->createMock(SymfonyStyle::class);
        $styler->expects($this->once())->method('note')->with('Running php unit ...');

        $this->service
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpUnitServiceConfig $conf) use (
                $executable,
                $config,
                $reportTarget,
                $reportFormat
            ) {
                $this->assertEquals($executable, $conf->getExecutableFile());
                $this->assertEquals($config, $conf->getConfigurationFile());
                $this->assertEquals($reportTarget, $conf->getReportTarget());
                $this->assertEquals($reportFormat, $conf->getReportFormat());

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

        $method = new ReflectionMethod(PhpUnitCommand::class, 'execute');
        $method->invoke($this->sut, $input, $output);
    }
}
