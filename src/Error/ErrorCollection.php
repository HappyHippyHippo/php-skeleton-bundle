<?php

namespace Hippy\Error;

use Hippy\Model\Collection;
use Hippy\Model\Model;
use InvalidArgumentException;

class ErrorCollection extends Collection
{
    /**
     * @param Model $item
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add(Model $item): self
    {
        if (!($item instanceof Error)) {
            throw new InvalidArgumentException('Invalid error instance');
        }
        $this->items[] = $item;
        return $this;
    }
}
