<?php

namespace Hippy\Exception;

use Hippy\Error\ErrorCollection;
use Hippy\Error\Error;
use Hippy\Model\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Exception extends HttpException
{
    /** @var ErrorCollection */
    protected ErrorCollection $errors;

    /** @var Model|null */
    protected ?Model $data;

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
     * @param Error $error
     * @return $this
     */
    public function addError(Error $error): self
    {
        $this->errors->add($error);
        return $this;
    }

    /**
     * @param ErrorCollection $errors
     * @return $this
     */
    public function addErrors(ErrorCollection $errors): self
    {
        /** @var Error $error */
        foreach ($errors as $error) {
            $this->errors->add($error);
        }
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
     * @param ErrorCollection $errors
     * @return $this
     */
    public function setErrors(ErrorCollection $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return Model|null
     */
    public function getData(): ?Model
    {
        return $this->data;
    }

    /**
     * @param Model $data
     * @return $this
     */
    public function setData(Model $data): self
    {
        $this->data = $data;
        return $this;
    }
}
