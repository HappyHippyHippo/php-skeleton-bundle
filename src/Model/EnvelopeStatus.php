<?php

namespace Hippy\Model;

use Hippy\Error\ErrorCollection;

/**
 * @method bool isSuccess()
 * @method ErrorCollection getErrors()
 */
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
}
