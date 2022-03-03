<?php

namespace HHH\Tests\Unit\Command\Quality\Service;

use HHH\Command\CommandException;
use HHH\Command\Quality\Service\AbstractService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

/** @coversDefaultClass \HHH\Command\Quality\Service\AbstractService */
class AbstractServiceTest extends TestCase
{
    /** @var AbstractService&MockObject */
    private AbstractService $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->sut = $this->getMockBuilder(AbstractService::class)
            ->onlyMethods(['fileExists', 'exec', 'system'])
            ->getMock();
    }

    /**
     * @return void
     * @covers ::checkExecutableFile
     * @throws ReflectionException
     */
    public function testCheckExecutableFileThrowsIfNotExists(): void
    {
        $file = '__dummy_file__';
        $this->sut->expects($this->once())->method('fileExists')->with($file)->willReturn(false);

        $this->expectExceptionObject(new CommandException('Executable not found', 'error'));
        $this->invokeCheckExecutableFile($file);
    }

    /**
     * @return void
     * @covers ::checkExecutableFile
     * @throws ReflectionException
     */
    public function testCheckExecutableFile(): void
    {
        $file = '__dummy_file__';
        $this->sut->expects($this->once())->method('fileExists')->with($file)->willReturn(true);

        $this->invokeCheckExecutableFile($file);
    }

    /**
     * @return void
     * @covers ::checkConfigurationFile
     * @throws ReflectionException
     */
    public function testCheckConfigurationFileThrowsIfNotExists(): void
    {
        $file = '__dummy_file__';
        $this->sut->expects($this->once())->method('fileExists')->with($file)->willReturn(false);

        $this->expectExceptionObject(new CommandException('Configuration file not found', 'error'));
        $this->invokeCheckConfigurationFile($file);
    }

    /**
     * @return void
     * @covers ::checkConfigurationFile
     * @throws ReflectionException
     */
    public function testCheckConfigurationFile(): void
    {
        $file = '__dummy_file__';
        $this->sut->expects($this->once())->method('fileExists')->with($file)->willReturn(true);

        $this->invokeCheckConfigurationFile($file);
    }

    /**
     * @return void
     * @covers ::checkReportDir
     * @throws ReflectionException
     */
    public function testCheckReportDirThrowsOnDirCreationError(): void
    {
        $dir = '__dummy_dir__';
        $output = ['_dummy_data__'];
        $returnVal = 1;

        $this->sut
            ->expects($this->once())
            ->method('exec')
            ->with(sprintf('mkdir -p %s', $dir), [], 0)
            ->willReturnCallback(function ($command, &$inOutput, &$inReturnVal) use ($output, $returnVal) {
                $inOutput = $output;
                $inReturnVal = $returnVal;

                return '';
            });

        $this->expectExceptionObject(
            new CommandException(
                'Error while creating report output dir : ' . implode($output),
                'error'
            )
        );

        $this->invokeCheckReportDir($dir);
    }

    /**
     * @return void
     * @covers ::checkReportDir
     * @throws ReflectionException
     */
    public function testCheckReportDir(): void
    {
        $dir = '__dummy_dir__';

        $this->sut
            ->expects($this->once())
            ->method('exec')
            ->with(sprintf('mkdir -p %s', $dir), [], 0)
            ->willReturn('');

        $this->invokeCheckReportDir($dir);
    }

    /**
     * @param string $configurationFile
     * @return void
     * @throws ReflectionException
     */
    private function invokeCheckExecutableFile(string $configurationFile): void
    {
        $method = new ReflectionMethod(AbstractService::class, 'checkExecutableFile');
        $method->invoke($this->sut, $configurationFile);
    }

    /**
     * @param string $file
     * @return void
     * @throws ReflectionException
     */
    private function invokeCheckConfigurationFile(string $file): void
    {
        $method = new ReflectionMethod(AbstractService::class, 'checkConfigurationFile');
        $method->invoke($this->sut, $file);
    }

    /**
     * @param string $dir
     * @return void
     * @throws ReflectionException
     */
    private function invokeCheckReportDir(string $dir): void
    {
        $method = new ReflectionMethod(AbstractService::class, 'checkReportDir');
        $method->invoke($this->sut, $dir);
    }
}
