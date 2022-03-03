<?php

namespace Hippy\Command\Quality;

use Hippy\Command\Quality\Config\PhpStanServiceConfig;
use Hippy\Command\Quality\Service\PhpStanService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhpStanCommand extends Command
{
    use CommandExecTrait;

    /** @var string */
    protected static $defaultName = 'quality:phpstan';

    /**
     * @param PhpStanService $service
     */
    public function __construct(protected PhpStanService $service)
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
            ->setDescription('Executes the phpstan.')
            ->setHelp('This command will executes the phpstan code quality application to check')
            ->addOption(
                'executable',
                'x',
                InputArgument::OPTIONAL,
                'The location of the phpstan executable',
                '/vendor/bin/phpstan'
            )
            ->addOption(
                'config',
                'c',
                InputArgument::OPTIONAL,
                'The location of the phpstan configuration file',
                '/phpstan.neon'
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
            $config = new PhpStanServiceConfig(
                $this->getOption($input, 'executable'),
                $this->getOption($input, 'config'),
            );
            $this->service->execute($config, $styler);
        });
    }
}
