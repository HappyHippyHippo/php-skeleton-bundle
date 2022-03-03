<?php

namespace HHH\Command\Quality;

use HHH\Command\Quality\Config\PhpCodeSnifferServiceConfig;
use HHH\Command\Quality\Service\PhpCodeSnifferService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhpCodeSnifferCommand extends Command
{
    use CommandExecTrait;

    /** @var string */
    protected static $defaultName = 'quality:phpcs';

    /**
     * @param PhpCodeSnifferService $service
     */
    public function __construct(protected PhpCodeSnifferService $service)
    {
        parent::__construct();
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Executes the phpcs.')
            ->setHelp('This command will executes the phpcs code quality application to check')
            ->addOption(
                'executable',
                'x',
                InputArgument::OPTIONAL,
                'The location of the phpcs executable',
                '/vendor/bin/phpcs'
            )
            ->addOption(
                'config',
                'c',
                InputArgument::OPTIONAL,
                'The location of the phpcs configuration xml file',
                '/phpcs.xml'
            )
            ->addOption(
                'report-type',
                't',
                InputArgument::OPTIONAL,
                'Type of report to be generated',
                'full'
            )
            ->addOption(
                'report-dir',
                'd',
                InputArgument::OPTIONAL,
                'The location where to write the execution output report',
                '/tests-reports'
            )
            ->addOption(
                'report-file',
                'r',
                InputArgument::OPTIONAL,
                'The name of the output report file',
                '/phpcs.txt'
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
            $styler->note('Running php code sniffer ...');
            $config = new PhpCodeSnifferServiceConfig(
                $this->getOption($input, 'executable'),
                $this->getOption($input, 'config'),
                $this->getOption($input, 'report-type'),
                $this->getOption($input, 'report-dir'),
                $this->getOption($input, 'report-file'),
            );
            $this->service->execute($config, $styler);
        });
    }
}
