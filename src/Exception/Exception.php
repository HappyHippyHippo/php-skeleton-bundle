<?php

namespace HHH\Exception;

use HHH\Error\ErrorCollection;
use HHH\Error\ErrorInterface;
use HHH\Model\ModelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Exception extends HttpException
{
    /** @var ErrorCollection */
    protected ErrorCollection $errors;

    /** @var ModelInterface|null */
    protected ?ModelInterface $data;

    /**
     * @param int $statusCode
     * @param string $message
     * @param Throwable|null $previous
     * @param array<string, string|string[]> $headers
     * @param int $code
     */
    public function __construct(
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        string $message = '',
        Throwable $previous = null,
        array $headers = [],
        int $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
        $this->errors = new ErrorCollection();
        $this->data = null;
    }

    /**
     * @param ErrorInterface $error
     * @return $this
     */
    public function addError(ErrorInterface $error): Exception
    {
        $this->errors->add($error);

        return $this;
    }

    /**
     * @param ErrorCollection $collection
     * @return $this
     */
    public function addErrors(ErrorCollection $collection): Exception
    {
        $this->errors = $collection;

        return $this;
    }

    /**
     * @return ErrorCollection
     */
    public function getErrors(): ErrorCollection
    {
        return $this->errors;
    }

    /**
     * @param ModelInterface|null $data
     * @return $this
     */
    public function setData(?ModelInterface $data): Exception
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return ModelInterface|null
     */
    public function getData(): ?ModelInterface
    {
        return $this->data;
    }
}
