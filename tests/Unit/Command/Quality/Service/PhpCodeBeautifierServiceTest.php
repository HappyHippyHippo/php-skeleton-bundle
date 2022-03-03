<?php

namespace Hippy\Tests\Unit\Command\Quality\Service;

use Hippy\Command\CommandException;
use Hippy\Command\Quality\Config\PhpCodeBeautifierServiceConfig;
use Hippy\Command\Quality\Service\PhpCodeBeautifierService;
use Hippy\Config\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command;

/** @coversDefaultClass \Hippy\Command\Quality\Service\PhpCodeBeautifierService */
class PhpCodeBeautifierServiceTest extends TestCase
{
    /** @var string */
    protected const ROOT = '__dummy_root__';

    /** @var ConfigInterface&MockObject */
    protected ConfigInterface $config;

    /** @var PhpCodeBeautifierServiceConfig&MockObject */
    protected PhpCodeBeautifierServiceConfig $phpcbfConfig;

    /** @var PhpCodeBeautifierService&MockObject */
    protected PhpCodeBeautifierService $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->phpcbfConfig = $this->createMock(PhpCodeBeautifierServiceConfig::class);
        $this->sut = $this->getMockBuilder(PhpCodeBeautifierService::class)
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
        $property = new ReflectionProperty(PhpCodeBeautifierService::class, 'config');
        $this->assertSame($this->config, $property->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::execute
     * @throws CommandException
     */
    public function testExecuteThrowsOnError(): void
    {
        $execFile = '__dummy_executable_file__';
        $configFile = '__dummy_configuration_file__';
        $return = '__dummy_return__';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpcbfConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpcbfConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s -p --standard=%s',
                self::ROOT . $execFile,
                self::ROOT . $configFile
            ),
            0
        )->willReturnCallback(function (string $command, int &$inReturn) use ($return) {
            return $inReturn = $return;
        });

        $this->expectExceptionObject(new CommandException('Errors/warnings have been found', 'warning'));
        $this->sut->execute($this->phpcbfConfig);
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

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpcbfConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpcbfConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s -p --standard=%s',
                self::ROOT . $execFile,
                self::ROOT . $configFile
            ),
            0
        );

        $this->assertEquals(Command::SUCCESS, $this->sut->execute($this->phpcbfConfig));
    }
}
