<?php

namespace HHH\Error;

use HHH\Model\Collection;
use HHH\Model\ModelInterface;
use InvalidArgumentException;

class ErrorCollection extends Collection
{
    /**
     * @param ModelInterface|ErrorInterface $item
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add(ModelInterface|ErrorInterface $item): ErrorCollection
    {
        if (!($item instanceof ErrorInterface)) {
            throw new InvalidArgumentException('Invalid error instance');
        }

        $this->items[] = $item;

        return $this;
    }
}
