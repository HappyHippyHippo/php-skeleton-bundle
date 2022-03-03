<?php

namespace HHH\Command\Quality;

use HHH\Command\Quality\Config\PhpCodeBeautifierServiceConfig;
use HHH\Command\Quality\Service\PhpCodeBeautifierService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhpCodeBeautifierCommand extends Command
{
    use CommandExecTrait;

    /** @var string */
    protected static $defaultName = 'quality:phpcbf';

    /**
     * @param PhpCodeBeautifierService $service
     */
    public function __construct(protected PhpCodeBeautifierService $service)
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
            ->setDescription('Executes the phpcbf.')
            ->setHelp('This command will executes the phpcbf code quality application to check')
            ->addOption(
                'executable',
                'x',
                InputArgument::OPTIONAL,
                'The location of the phpcbf executable',
                '/vendor/bin/phpcbf'
            )
            ->addOption(
                'config',
                'c',
                InputArgument::OPTIONAL,
                'The location of the phpcbf configuration xml file',
                '/phpcs.xml'
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
            $styler->note('Running php code beautifier ...');
            $config = new PhpCodeBeautifierServiceConfig(
                $this->getOption($input, 'executable'),
                $this->getOption($input, 'config'),
            );
            $this->service->execute($config);
        });
    }
}
