<?php

namespace Hippy\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RedirectException extends Exception
{
    /**
     * @param string $url
     * @param int $statusCode
     * @param string $message
     * @param Throwable|null $previous
     * @param array<string, string> $headers
     * @param int $code
     */
    public function __construct(
        protected string $url,
        int $statusCode = Response::HTTP_TEMPORARY_REDIRECT,
        string $message = '',
        Throwable $previous = null,
        array $headers = [],
        int $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
