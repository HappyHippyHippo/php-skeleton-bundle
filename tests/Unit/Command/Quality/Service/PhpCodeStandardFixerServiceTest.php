<?php

namespace HHH\Tests\Unit\Command\Quality\Service;

use HHH\Command\CommandException;
use HHH\Command\Quality\Config\PhpCodeStandardFixerServiceConfig;
use HHH\Command\Quality\Service\PhpCodeStandardFixerService;
use HHH\Config\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \HHH\Command\Quality\Service\PhpCodeStandardFixerService */
class PhpCodeStandardFixerServiceTest extends TestCase
{
    /** @var string */
    protected const ROOT = '__dummy_root__';

    /** @var ConfigInterface&MockObject */
    protected ConfigInterface $config;

    /** @var PhpCodeStandardFixerServiceConfig&MockObject */
    protected PhpCodeStandardFixerServiceConfig $phpcsfixerConfig;

    /** @var SymfonyStyle&MockObject */
    protected SymfonyStyle $styler;

    /** @var PhpCodeStandardFixerService&MockObject */
    protected PhpCodeStandardFixerService $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->phpcsfixerConfig = $this->createMock(PhpCodeStandardFixerServiceConfig::class);
        $this->styler = $this->createMock(SymfonyStyle::class);
        $this->sut = $this->getMockBuilder(PhpCodeStandardFixerService::class)
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
        $property = new ReflectionProperty(PhpCodeStandardFixerService::class, 'config');
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
        $this->phpcsfixerConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpcsfixerConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s fix -v --show-progress dots --using-cache no --config="%s"',
                self::ROOT . $execFile,
                self::ROOT . $configFile
            ),
            0
        )->willReturnCallback(function (string $command, int &$inReturn) use ($return) {
            return $inReturn = $return;
        });

        try {
            $this->sut->execute($this->phpcsfixerConfig, $this->styler);
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
        $this->phpcsfixerConfig->expects($this->once())->method('getExecutableFile')->willReturn($execFile);
        $this->phpcsfixerConfig->expects($this->once())->method('getConfigurationFile')->willReturn($configFile);

        $this->sut->expects($this->once())->method('checkExecutableFile')->with(self::ROOT . $execFile);
        $this->sut->expects($this->once())->method('checkConfigurationFile')->with(self::ROOT . $configFile);
        $this->sut->expects($this->once())->method('system')->with(
            sprintf(
                '%s fix -v --show-progress dots --using-cache no --config="%s"',
                self::ROOT . $execFile,
                self::ROOT . $configFile
            ),
            0
        );

        $this->assertEquals(Command::SUCCESS, $this->sut->execute($this->phpcsfixerConfig, $this->styler));
    }
}
