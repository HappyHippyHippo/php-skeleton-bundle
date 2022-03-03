<?php

namespace HHH\Tests\Unit\Command\Quality;

use HHH\Command\CommandException;
use HHH\Command\Quality\CommandExecTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \HHH\Command\Quality\CommandExecTrait */
class CommandExecTraitTest extends TestCase
{
    /** @var InputInterface&MockObject  */
    protected InputInterface $input;

    /** @var OutputInterface&MockObject */
    protected OutputInterface $output;

    /** @var SymfonyStyle&MockObject */
    protected SymfonyStyle $styler;

    /** @var MockObject */
    protected MockObject $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->styler = $this->createMock(SymfonyStyle::class);

        $this->sut = $this->getMockForTrait(
            CommandExecTrait::class,
            [],
            '',
            true,
            true,
            true,
            ['execute', 'getStyler']
        );
        $this->sut
            ->method('getStyler')
            ->with($this->input, $this->output)
            ->willReturn($this->styler);
    }

    /**
     * @return void
     * @covers ::exec
     * @throws ReflectionException
     */
    public function testExec(): void
    {
        $message = '__dummy_test_message__';
        $isCallbackCalled = false;

        $this->styler->expects($this->once())->method('writeln')->with($message);
        $this->styler->expects($this->once())->method('success')->with('No error/warning was found.');

        $callback = function (SymfonyStyle $styler) use (&$isCallbackCalled, $message) {
            $this->assertInstanceOf(SymfonyStyle::class, $styler);
            $styler->writeln($message);
            $isCallbackCalled = true;
        };

        $this->assertEquals(Command::SUCCESS, $this->invokeExec($callback));
        $this->assertTrue($isCallbackCalled, 'Callback was not called');
    }

    /**
     * @return void
     * @covers ::exec
     * @throws ReflectionException
     */
    public function testExecOnException(): void
    {
        $message = '__dummy_test_message__';
        $exceptionMessage = '__dummy_test_exception_message__';
        $exceptionType = 'warning';
        $isCallbackCalled = false;
        $isPreCallbackCalled = false;
        $isPostCallbackCalled = false;

        $this->styler->expects($this->once())->method('writeln')->with($message);
        $this->styler->expects($this->once())->method($exceptionType)->with($exceptionMessage);

        $preCallback = function () use (&$isPreCallbackCalled) {
            $isPreCallbackCalled = true;
        };
        $postCallback = function () use (&$isPostCallbackCalled) {
            $isPostCallbackCalled = true;
        };
        $exception = new CommandException($exceptionMessage, $exceptionType, $preCallback, $postCallback);

        $callback = function (SymfonyStyle $styler) use (&$isCallbackCalled, $message, $exception) {
            $this->assertInstanceOf(SymfonyStyle::class, $styler);
            $styler->writeln($message);
            $isCallbackCalled = true;

            throw $exception;
        };

        $this->assertEquals(Command::FAILURE, $this->invokeExec($callback));

        $this->assertTrue($isCallbackCalled, 'Callback was not called');
        $this->assertTrue($isPreCallbackCalled, 'Pre exception callback was not called');
        $this->assertTrue($isPostCallbackCalled, 'Post exception callback was not called');
    }

    /**
     * @param callable $callback
     * @return mixed
     * @throws ReflectionException
     */
    private function invokeExec(callable $callback): mixed
    {
        $method = new ReflectionMethod(get_class($this->sut), 'exec');
        return $method->invoke($this->sut, $this->input, $this->output, $callback);
    }
}
