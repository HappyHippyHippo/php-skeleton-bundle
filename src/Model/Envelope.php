<?php

namespace HHH\Model;

use HHH\Error\ErrorCollection;
use InvalidArgumentException;
use JsonSerializable;

class Envelope extends Model
{
    /** @var EnvelopeStatus */
    protected EnvelopeStatus $status;

    /** @var ModelInterface|null */
    protected ?ModelInterface $data = null;

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

    /**
     * @return EnvelopeStatus
     */
    public function getStatus(): EnvelopeStatus
    {
        return $this->status;
    }

    /**
     * @return ModelInterface|null
     */
    public function getData(): ?ModelInterface
    {
        return $this->data;
    }

    /**
     * @param ModelInterface $data
     * @return $this
     */
    public function setData(ModelInterface $data): Envelope
    {
        $this->data = $data;
        return $this;
    }
}
