<?php

namespace Hippy\Error;

use Hippy\Model\ModelInterface;

interface ErrorInterface extends ModelInterface
{
    /**
     * @return int|string
     */
    public function getService(): int|string;

    /**
     * @param int|string $code
     * @return ErrorInterface
     */
    public function setService(int|string $code): ErrorInterface;

    /**
     * @return int|string
     */
    public function getEndpoint(): int|string;

    /**
     * @param int|string $code
     * @return ErrorInterface
     */
    public function setEndpoint(int|string $code): ErrorInterface;

    /**
     * @return int|string
     */
    public function getParam(): int|string;

    /**
     * @param int|string $code
     * @return ErrorInterface
     */
    public function setParam(int|string $code): ErrorInterface;

    /**
     * @return int|string
     */
    public function getCode(): int|string;

    /**
     * @param int|string $code
     * @return ErrorInterface
     */
    public function setCode(int|string $code): ErrorInterface;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return ErrorInterface
     */
    public function setMessage(string $message): ErrorInterface;
}
