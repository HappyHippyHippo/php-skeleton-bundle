<?php

namespace Hippy\Model;

use Hippy\Error\ErrorCollection;

/**
 * @method EnvelopeStatus getStatus()
 * @method Model|null getData()
 * @method Envelope setData(Model|null $data)
 */
class Envelope extends Model
{
    /** @var EnvelopeStatus */
    protected EnvelopeStatus $status;

    /** @var Model|null */
    protected ?Model $data = null;

    /**
     * @param ErrorCollection|null $errors
     */
    public function __construct(?ErrorCollection $errors = null)
    {
        parent::__construct([
            'status' => new EnvelopeStatus(
                null === $errors || 0 === count($errors),
                $errors ?? new ErrorCollection()
            ),
            'data' => null,
        ]);
    }
}
