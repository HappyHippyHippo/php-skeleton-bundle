<?php

namespace Hippy\Error;

use Throwable;

class ExceptionError extends Error
{
    /** @var string */
    protected string $file;

    /** @var int|string */
    protected int|string $line;

    /** @var array<int, array<string, mixed>> */
    protected array $trace;

    /**
     * @param int $code
     * @param string $message
     * @param Throwable $exception
     */
    public function __construct(int $code, string $message, Throwable $exception)
    {
        parent::__construct($code, $message);

        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = $exception->getTrace();
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return int|string
     */
    public function getLine(): int|string
    {
        return $this->line;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTrace(): array
    {
        return $this->trace;
    }
}
