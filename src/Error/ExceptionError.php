<?php

namespace Hippy\Error;

use Throwable;

/**
 * @method string getFile()
 * @method string|int getLine()
 * @method array<int, mixed> getTrace()
 */
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
}
