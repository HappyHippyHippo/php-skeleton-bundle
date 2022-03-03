<?php

namespace HHH\Command\Quality;

use HHH\Command\Quality\Config\PhpCodeStandardFixerServiceConfig;
use HHH\Command\Quality\Service\PhpCodeStandardFixerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhpCodeStandardFixerCommand extends Command
{
    use CommandExecTrait;

    /** @var string */
    protected static $defaultName = 'quality:phpfixer';

    /**
     * @param PhpCodeStandardFixerService $service
     */
    public function __construct(protected PhpCodeStandardFixerService $service)
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
            ->setDescription('Executes the php-cs-fixer.')
            ->setHelp('This command will executes the php-cs-fixer code quality application to check')
            ->addOption(
                'executable',
                'x',
                InputArgument::OPTIONAL,
                'The location of the php-cs-fixer executable',
                '/vendor/bin/php-cs-fixer'
            )
            ->addOption(
                'config',
                'c',
                InputArgument::OPTIONAL,
                'The location of the php-cs-fixer configuration file',
                '/.php-cs-fixer.php'
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
            $styler->note('Running php stan ...');
            $config = new PhpCodeStandardFixerServiceConfig(
                $this->getOption($input, 'executable'),
                $this->getOption($input, 'config'),
            );
            $this->service->execute($config, $styler);
        });
    }
}
