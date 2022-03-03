<?php

namespace Hippy\Tests\Unit\Command\Quality;

use Hippy\Command\Quality\Config\PhpCodeStandardFixerServiceConfig;
use Hippy\Command\Quality\PhpCodeStandardFixerCommand;
use Hippy\Command\Quality\Service\PhpCodeStandardFixerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \Hippy\Command\Quality\PhpCodeStandardFixerCommand */
class PhpCodeStandardFixerCommandTest extends TestCase
{
    /** @var PhpCodeStandardFixerService&MockObject */
    protected PhpCodeStandardFixerService $service;

    /** @var InputInterface&MockObject */
    protected InputInterface $input;

    /** @var OutputInterface&MockObject */
    protected OutputInterface $output;

    /** @var PhpCodeStandardFixerCommand&MockObject */
    protected PhpCodeStandardFixerCommand $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->service = $this->createMock(PhpCodeStandardFixerService::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->sut = $this->getMockBuilder(PhpCodeStandardFixerCommand::class)
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
        $service = new ReflectionProperty(PhpCodeStandardFixerCommand::class, 'service');
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

        $input = $this->createMock(InputInterface::class);
        $input
            ->expects($this->exactly(2))
            ->method('getOption')
            ->withConsecutive(['executable'], ['config'])
            ->willReturnOnConsecutiveCalls($executable, $config);
        $output = $this->createMock(OutputInterface::class);

        $styler = $this->createMock(SymfonyStyle::class);
        $styler->expects($this->once())->method('note')->with('Running php stan ...');

        $this->service
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpCodeStandardFixerServiceConfig $conf) use (
                $executable,
                $config,
            ) {
                $this->assertEquals($executable, $conf->getExecutableFile());
                $this->assertEquals($config, $conf->getConfigurationFile());

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

        $method = new ReflectionMethod(PhpCodeStandardFixerCommand::class, 'execute');
        $method->invoke($this->sut, $input, $output);
    }
}
