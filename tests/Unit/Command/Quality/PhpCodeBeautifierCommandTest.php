<?php

namespace HHH\Tests\Unit\Command\Quality;

use HHH\Command\Quality\Config\PhpCodeBeautifierServiceConfig;
use HHH\Command\Quality\PhpCodeBeautifierCommand;
use HHH\Command\Quality\Service\PhpCodeBeautifierService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \HHH\Command\Quality\PhpCodeBeautifierCommand */
class PhpCodeBeautifierCommandTest extends TestCase
{
    /** @var PhpCodeBeautifierService&MockObject */
    protected PhpCodeBeautifierService $service;

    /** @var InputInterface&MockObject */
    protected InputInterface $input;

    /** @var OutputInterface&MockObject  */
    protected OutputInterface $output;

    /** @var PhpCodeBeautifierCommand&MockObject  */
    protected PhpCodeBeautifierCommand $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->service = $this->createMock(PhpCodeBeautifierService::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->sut = $this->getMockBuilder(PhpCodeBeautifierCommand::class)
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
        $service = new ReflectionProperty(PhpCodeBeautifierCommand::class, 'service');
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
        $styler->expects($this->once())->method('note')->with('Running php code beautifier ...');

        $this->service
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpCodeBeautifierServiceConfig $conf) use ($executable, $config) {
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

        $method = new ReflectionMethod(PhpCodeBeautifierCommand::class, 'execute');
        $method->invoke($this->sut, $input, $output);
    }
}
