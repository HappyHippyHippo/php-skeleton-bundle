<?php

namespace Hippy\Error;

use Hippy\Model\Collection;

class ErrorCollection extends Collection
{
    /**
     * @param Error[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(Error::class, $items);
    }
}
