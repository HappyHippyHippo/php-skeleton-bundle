<?php

namespace Hippy\Command\Quality;

use Hippy\Command\CommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

trait CommandExecTrait
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param callable $callback
     * @return int
     */
    protected function exec(InputInterface $input, OutputInterface $output, callable $callback): int
    {
        $styler = $this->getStyler($input, $output);

        try {
            $callback($styler);
        } catch (CommandException $exception) {
            $callback = $exception->getPreMessageAction();
            if ($callback) {
                $callback($styler);
            }

            $styler->{$exception->getMessageType()}($exception->getMessage());

            $callback = $exception->getPostMessageAction();
            if ($callback) {
                $callback($styler);
            }

            return Command::FAILURE;
        }

        $styler->success('No error/warning was found.');

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param string $name
     * @return string
     */
    protected function getOption(InputInterface $input, string $name): string
    {
        $value = $input->getOption($name);
        return (
            (!is_array($value))
            && (
                (!is_object($value) && settype($value, 'string') !== false)
                || (is_object($value) && method_exists($value, '__toString'))
            )
        ) ? trim('' . $value) : '';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return SymfonyStyle
     * @codeCoverageIgnore
     */
    protected function getStyler(InputInterface $input, OutputInterface $output): SymfonyStyle
    {
        return new SymfonyStyle($input, $output);
    }
}
