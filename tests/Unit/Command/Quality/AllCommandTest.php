<?php

namespace Hippy\Tests\Unit\Command\Quality;

use Hippy\Command\Quality\AllCommand;
use Hippy\Command\Quality\Config\PhpCodeBeautifierServiceConfig;
use Hippy\Command\Quality\Config\PhpCodeSnifferServiceConfig;
use Hippy\Command\Quality\Config\PhpCodeStandardFixerServiceConfig;
use Hippy\Command\Quality\Config\PhpStanServiceConfig;
use Hippy\Command\Quality\Config\PhpUnitServiceConfig;
use Hippy\Command\Quality\Service\PhpCodeBeautifierService;
use Hippy\Command\Quality\Service\PhpCodeSnifferService;
use Hippy\Command\Quality\Service\PhpCodeStandardFixerService;
use Hippy\Command\Quality\Service\PhpStanService;
use Hippy\Command\Quality\Service\PhpUnitService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @coversDefaultClass \Hippy\Command\Quality\AllCommand */
class AllCommandTest extends TestCase
{
    /** @var PhpCodeBeautifierService&MockObject */
    protected PhpCodeBeautifierService $phpcbf;

    /** @var PhpCodeStandardFixerService&MockObject */
    protected PhpCodeStandardFixerService $phpcsfixer;

    /** @var PhpCodeSnifferService&MockObject */
    protected PhpCodeSnifferService $phpcs;

    /** @var PhpStanService&MockObject */
    protected PhpStanService $phpstan;

    /** @var PhpUnitService&MockObject */
    protected PhpUnitService $phpunit;

    /** @var InputInterface&MockObject */
    protected InputInterface $input;

    /** @var OutputInterface&MockObject */
    protected OutputInterface $output;

    /** @var AllCommand&MockObject */
    protected AllCommand $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->phpcbf = $this->createMock(PhpCodeBeautifierService::class);
        $this->phpcsfixer = $this->createMock(PhpCodeStandardFixerService::class);
        $this->phpcs = $this->createMock(PhpCodeSnifferService::class);
        $this->phpstan = $this->createMock(PhpStanService::class);
        $this->phpunit = $this->createMock(PhpUnitService::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->sut = $this->getMockBuilder(AllCommand::class)
            ->setConstructorArgs([
                $this->phpcbf,
                $this->phpcsfixer,
                $this->phpcs,
                $this->phpstan,
                $this->phpunit,
            ])
            ->onlyMethods(['exec'])
            ->getMock();
    }

    /**
     * @return void
     * @covers ::__construct
     * @throws ReflectionException
     */
    public function testConstruct(): void
    {
        $services = ['phpcbf', 'phpcsfixer', 'phpcs', 'phpstan', 'phpunit'];

        foreach ($services as $service) {
            $serv = new ReflectionProperty(AllCommand::class, $service);
            $this->assertSame($this->$service, $serv->getValue($this->sut));
        }
    }

    /**
     * @return void
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testExecute(): void
    {
        $phpcbfExec = '__dummy_phpcbf_executable__';
        $phpcbfConfig = '__dummy_phpcbf_configuration__';

        $phpcsfixerExec = '__dummy_phpcsfixer_executable__';
        $phpcsfixerConfig = '__dummy_phpcsfixer_configuration__';

        $phpcsExec = '__dummy_phpcs_executable__';
        $phpcsConfig = '__dummy_phpcs_configuration__';
        $phpcsReportType = '__dummy_phpcs_report_type__';
        $phpcsReportDir = '__dummy_phpcs_report_dir__';
        $phpcsReportFile = '__dummy_phpcs_report_file__';

        $phpstanExec = '__dummy_phpstan_executable__';
        $phpstanConfig = '__dummy_phpstan_configuration__';

        $phpunitExec = '__dummy_phpunit_executable__';
        $phpunitConfig = '__dummy_phpunit_configuration__';
        $phpunitReportTarget = '__dummy_phpunit_report_target__';
        $phpunitReportFormat = '__dummy_phpunit_report_format__';

        $input = $this->createMock(InputInterface::class);
        $input
            ->expects($this->exactly(15))
            ->method('getOption')
            ->withConsecutive(
                ['phpcbf-executable'],
                ['phpcbf-config'],
                ['phpcsfixer-executable'],
                ['phpcsfixer-config'],
                ['phpcs-executable'],
                ['phpcs-config'],
                ['phpcs-report-type'],
                ['phpcs-report-dir'],
                ['phpcs-report-file'],
                ['phpstan-executable'],
                ['phpstan-config'],
                ['phpunit-executable'],
                ['phpunit-config'],
                ['phpunit-report-target'],
                ['phpunit-report-format']
            )
            ->willReturnOnConsecutiveCalls(
                $phpcbfExec,
                $phpcbfConfig,
                $phpcsfixerExec,
                $phpcsfixerConfig,
                $phpcsExec,
                $phpcsConfig,
                $phpcsReportType,
                $phpcsReportDir,
                $phpcsReportFile,
                $phpstanExec,
                $phpstanConfig,
                $phpunitExec,
                $phpunitConfig,
                $phpunitReportTarget,
                $phpunitReportFormat
            );
        $output = $this->createMock(OutputInterface::class);

        $styler = $this->createMock(SymfonyStyle::class);
        $styler
            ->expects($this->exactly(5))
            ->method('note')
            ->withConsecutive(
                ['Running php code beautifier ...'],
                ['Running php cs fixer ...'],
                ['Running php code sniffer ...'],
                ['Running php stan ...'],
                ['Running php unit ...']
            );

        $this->phpcbf
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpCodeBeautifierServiceConfig $conf) use (
                $phpcbfExec,
                $phpcbfConfig
            ) {
                $this->assertEquals($phpcbfExec, $conf->getExecutableFile());
                $this->assertEquals($phpcbfConfig, $conf->getConfigurationFile());

                return true;
            }));

        $this->phpcsfixer
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpCodeStandardFixerServiceConfig $conf) use (
                $phpcsfixerExec,
                $phpcsfixerConfig,
            ) {
                $this->assertEquals($phpcsfixerExec, $conf->getExecutableFile());
                $this->assertEquals($phpcsfixerConfig, $conf->getConfigurationFile());

                return true;
            }));

        $this->phpcs
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpCodeSnifferServiceConfig $conf) use (
                $phpcsExec,
                $phpcsConfig,
                $phpcsReportType,
                $phpcsReportDir,
                $phpcsReportFile
            ) {
                $this->assertEquals($phpcsExec, $conf->getExecutableFile());
                $this->assertEquals($phpcsConfig, $conf->getConfigurationFile());
                $this->assertEquals($phpcsReportType, $conf->getReportType());
                $this->assertEquals($phpcsReportDir, $conf->getReportDir());
                $this->assertEquals($phpcsReportFile, $conf->getReportFile());

                return true;
            }));

        $this->phpstan
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpStanServiceConfig $conf) use (
                $phpstanExec,
                $phpstanConfig,
            ) {
                $this->assertEquals($phpstanExec, $conf->getExecutableFile());
                $this->assertEquals($phpstanConfig, $conf->getConfigurationFile());

                return true;
            }));

        $this->phpunit
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (PhpUnitServiceConfig $conf) use (
                $phpunitExec,
                $phpunitConfig,
                $phpunitReportTarget,
                $phpunitReportFormat
            ) {
                $this->assertEquals($phpunitExec, $conf->getExecutableFile());
                $this->assertEquals($phpunitConfig, $conf->getConfigurationFile());
                $this->assertEquals($phpunitReportTarget, $conf->getReportTarget());
                $this->assertEquals($phpunitReportFormat, $conf->getReportFormat());

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

        $method = new ReflectionMethod(AllCommand::class, 'execute');
        $method->invoke($this->sut, $input, $output);
    }
}
