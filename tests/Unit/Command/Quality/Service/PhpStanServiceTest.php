<?php

namespace HHH\Tests\Unit\Command\Quality\Service;

use HHH\Command\CommandException;
use HHH\Command\Quality\Config\PhpStanServiceConfig;
use HHH\Command\Quality\Service\PhpStanService;
use HHH\Config\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \HHH\Command\Quality\Service\PhpStanService */
class PhpStanServiceTest extends TestCase
{
    /** @var string */
    protected const ROOT = '__dummy_root__';

    /** @var ConfigInterface&MockObject */
    protected ConfigInterface $config;

    /** @var PhpStanServiceConfig&MockObject */
    protected PhpStanServiceConfig $phpstanConfig;

    /** @var SymfonyStyle&MockObject */
    protected SymfonyStyle $styler;

    /** @var PhpStanService&MockObject */
    protected PhpStanService $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->phpstanConfig = $this->createMock(PhpStanServiceConfig::class);
        $this->styler = $this->createMock(SymfonyStyle::class);
        $this->sut = $this->getMockBuilder(PhpStanService::class)
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
        $property = new ReflectionProperty(PhpStanService::class, 'config');
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
        $return = '__dummy_return__';

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpstanConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpstanConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s analyse -c "%s"',
                self::ROOT . $execFile,
                self::ROOT . $configFile
            ),
            0
        )->willReturnCallback(function (string $command, int &$inReturn) use ($return) {
            return $inReturn = $return;
        });

        try {
            $this->sut->execute($this->phpstanConfig, $this->styler);
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

        $this->config->method('getRoot')->willReturn(self::ROOT);
        $this->phpstanConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpstanConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s analyse -c "%s"',
                self::ROOT . $execFile,
                self::ROOT . $configFile
            ),
            0
        );

        $this->assertEquals(Command::SUCCESS, $this->sut->execute($this->phpstanConfig, $this->styler));
    }
}
