<?php

namespace HHH\Command\Quality;

use HHH\Command\Quality\Config\PhpUnitServiceConfig;
use HHH\Command\Quality\Service\PhpUnitService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhpUnitCommand extends Command
{
    use CommandExecTrait;

    /** @var string */
    protected static $defaultName = 'quality:phpunit';

    /**
     * @param PhpUnitService $service
     */
    public function __construct(protected PhpUnitService $service)
    {
        parent::__construct();
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function configure()
    {
        $this
            ->setDescription('Executes the phpunit.')
            ->setHelp('This command will executes the phpunit code quality application to check')
            ->addOption(
                'executable',
                'x',
                InputArgument::OPTIONAL,
                'The location of the phpunit executable',
                '/vendor/bin/phpunit'
            )
            ->addOption(
                'config',
                'c',
                InputArgument::OPTIONAL,
                'The location of the phpunit configuration xml file',
                '/phpunit.xml'
            )
            ->addOption(
                'report-target',
                't',
                InputArgument::OPTIONAL,
                'The location where to write the execution output report',
                '/tests-reports/phpunit/'
            )
            ->addOption(
                'report-format',
                'f',
                InputArgument::OPTIONAL,
                'The format of the coverage output report',
                'html'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->exec($input, $output, function (SymfonyStyle $styler) use ($input) {
            $styler->note('Running php unit ...');
            $config = new PhpUnitServiceConfig(
                $this->getOption($input, 'executable'),
                $this->getOption($input, 'config'),
                $this->getOption($input, 'report-target'),
                $this->getOption($input, 'report-format'),
            );
            $this->service->execute($config);
        });
    }
}
