<?php

namespace HHH\Command\Quality;

use HHH\Command\Quality\Config\PhpCodeBeautifierServiceConfig;
use HHH\Command\Quality\Config\PhpCodeStandardFixerServiceConfig;
use HHH\Command\Quality\Config\PhpCodeSnifferServiceConfig;
use HHH\Command\Quality\Config\PhpStanServiceConfig;
use HHH\Command\Quality\Config\PhpUnitServiceConfig;
use HHH\Command\Quality\Service\PhpCodeBeautifierService;
use HHH\Command\Quality\Service\PhpCodeStandardFixerService;
use HHH\Command\Quality\Service\PhpCodeSnifferService;
use HHH\Command\Quality\Service\PhpStanService;
use HHH\Command\Quality\Service\PhpUnitService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AllCommand extends Command
{
    use CommandExecTrait;

    /** @var string */
    protected static $defaultName = 'quality:all';

    /**
     * @param PhpCodeBeautifierService $phpcbf
     * @param PhpCodeStandardFixerService $phpcsfixer
     * @param PhpCodeSnifferService $phpcs
     * @param PhpStanService $phpstan
     * @param PhpUnitService $phpunit
     */
    public function __construct(
        protected PhpCodeBeautifierService $phpcbf,
        protected PhpCodeStandardFixerService $phpcsfixer,
        protected PhpCodeSnifferService $phpcs,
        protected PhpStanService $phpstan,
        protected PhpUnitService $phpunit,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Executes all code quality tools.')
            ->setHelp('This command will executes all the code quality applications');

        $this->configCodeBeautifier();
        $this->configCodeStandardFixer();
        $this->configCodeSniffer();
        $this->configStan();
        $this->configPhpUnit();
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function configCodeBeautifier(): void
    {
        $this
            ->addOption(
                'phpcbf-executable',
                '',
                InputArgument::OPTIONAL,
                'The location of the phpcbf executable',
                '/vendor/bin/phpcbf'
            )
            ->addOption(
                'phpcbf-config',
                '',
                InputArgument::OPTIONAL,
                'The location of the phpcbf configuration xml file',
                '/phpcs.xml'
            );
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function configCodeStandardFixer(): void
    {
        $this
            ->addOption(
                'phpcsfixer-executable',
                '',
                InputArgument::OPTIONAL,
                'The location of the php-cs-fixer executable',
                '/vendor/bin/php-cs-fixer'
            )
            ->addOption(
                'phpcsfixer-config',
                '',
                InputArgument::OPTIONAL,
                'The location of the php-cs-fixer configuration file',
                '/.php-cs-fixer.php'
            );
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function configCodeSniffer(): void
    {
        $this
            ->addOption(
                'phpcs-executable',
                '',
                InputArgument::OPTIONAL,
                'The location of the phpcs executable',
                '/vendor/bin/phpcs'
            )
            ->addOption(
                'phpcs-config',
                '',
                InputArgument::OPTIONAL,
                'The location of the phpcs configuration xml file',
                '/phpcs.xml'
            )
            ->addOption(
                'phpcs-report-type',
                '',
                InputArgument::OPTIONAL,
                'Type of report to be generated',
                'full'
            )
            ->addOption(
                'phpcs-report-dir',
                '',
                InputArgument::OPTIONAL,
                'The location where to write the execution output report',
                '/tests-reports'
            )
            ->addOption(
                'phpcs-report-file',
                '',
                InputArgument::OPTIONAL,
                'The name of the output report file',
                '/phpcs.txt'
            );
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function configStan(): void
    {
        $this
            ->addOption(
                'phpstan-executable',
                '',
                InputArgument::OPTIONAL,
                'The location of the phpstan executable',
                '/vendor/bin/phpstan'
            )
            ->addOption(
                'phpstan-config',
                '',
                InputArgument::OPTIONAL,
                'The location of the phpstan configuration file',
                '/phpstan.neon'
            );
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function configPhpUnit(): void
    {
        $this
            ->addOption(
                'phpunit-executable',
                '',
                InputArgument::OPTIONAL,
                'The location of the phpunit executable',
                '/vendor/bin/phpunit'
            )
            ->addOption(
                'phpunit-config',
                '',
                InputArgument::OPTIONAL,
                'The location of the phpunit configuration xml file',
                '/phpunit.xml'
            )
            ->addOption(
                'phpunit-report-target',
                '',
                InputArgument::OPTIONAL,
                'The location where to write the execution output report',
                '/tests-reports/phpunit/'
            )
            ->addOption(
                'phpunit-report-format',
                '',
                InputArgument::OPTIONAL,
                'The format of the coverage output report',
                'html'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->exec($input, $output, function (SymfonyStyle $styler) use ($input) {
            $styler->note('Running php code beautifier ...');
            $config = new PhpCodeBeautifierServiceConfig(
                $this->getOption($input, 'phpcbf-executable'),
                $this->getOption($input, 'phpcbf-config'),
            );
            $this->phpcbf->execute($config);

            $styler->note('Running php cs fixer ...');
            $config = new PhpCodeStandardFixerServiceConfig(
                $this->getOption($input, 'phpcsfixer-executable'),
                $this->getOption($input, 'phpcsfixer-config'),
            );
            $this->phpcsfixer->execute($config, $styler);

            $styler->note('Running php code sniffer ...');
            $config = new PhpCodeSnifferServiceConfig(
                $this->getOption($input, 'phpcs-executable'),
                $this->getOption($input, 'phpcs-config'),
                $this->getOption($input, 'phpcs-report-type'),
                $this->getOption($input, 'phpcs-report-dir'),
                $this->getOption($input, 'phpcs-report-file'),
            );
            $this->phpcs->execute($config, $styler);

            $styler->note('Running php stan ...');
            $config = new PhpStanServiceConfig(
                $this->getOption($input, 'phpstan-executable'),
                $this->getOption($input, 'phpstan-config'),
            );
            $this->phpstan->execute($config, $styler);

            $styler->note('Running php unit ...');
            $config = new PhpUnitServiceConfig(
                $this->getOption($input, 'phpunit-executable'),
                $this->getOption($input, 'phpunit-config'),
                $this->getOption($input, 'phpunit-report-target'),
                $this->getOption($input, 'phpunit-report-format'),
            );
            $this->phpunit->execute($config);
        });
    }
}
