<?php

namespace Hippy\Model;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<int, ModelInterface>
 * @extends ArrayAccess<int, ModelInterface>
 */
interface CollectionInterface extends ModelInterface, Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @param callable|null $identifier
     * @return CollectionInterface<ModelInterface>
     */
    public function setIdentifier(?callable $identifier): CollectionInterface;

    /**
     * @return array<int|string, ModelInterface>
     */
    public function items(): array;

    /**
     * @param callable $func
     * @return CollectionInterface<ModelInterface>
     */
    public function each(callable $func): CollectionInterface;

    /**
     * @param callable $compare
     * @return CollectionInterface<ModelInterface>
     */
    public function filter(callable $compare): CollectionInterface;

    /**
     * @param callable $compare
     * @return ModelInterface|null
     */
    public function find(callable $compare): ?ModelInterface;

    /**
     * @param ModelInterface $item
     * @return CollectionInterface<ModelInterface>
     * @throws InvalidArgumentException
     */
    public function add(ModelInterface $item): CollectionInterface;
}
