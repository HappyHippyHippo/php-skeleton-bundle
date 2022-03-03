<?php

namespace HHH\Model;

use HHH\Error\ErrorCollection;

class EnvelopeStatus extends Model
{
    /** @var ErrorCollection */
    protected ErrorCollection $errors;

    /**
     * @param bool $success
     * @param ErrorCollection|null $errors
     */
    public function __construct(protected bool $success, ?ErrorCollection $errors = null)
    {
        parent::__construct(['errors' => $errors ?? new ErrorCollection()]);
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return ErrorCollection
     */
    public function getErrors(): ErrorCollection
    {
        return $this->errors;
    }
}
