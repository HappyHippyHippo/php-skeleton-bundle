<?php

namespace HHH\Model;

use ArrayIterator;
use InvalidArgumentException;

abstract class Collection extends Model implements CollectionInterface
{
    /** @var ModelInterface[] */
    protected array $items = [];

    /** @var callable|null */
    protected mixed $identifier = null;

    /**
     * @param ModelInterface[] $items
     * @throws InvalidArgumentException
     */
    public function __construct(array $items = [])
    {
        parent::__construct();

        foreach ($items as $item) {
            $this->add($item);
        }
        $this->identifier = null;
    }

    /**
     * @param callable|null $identifier
     * @return $this
     */
    public function setIdentifier(?callable $identifier): CollectionInterface
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return ModelInterface[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param int|string $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param int|string $offset
     * @return ModelInterface
     */
    public function offsetGet(mixed $offset): ModelInterface
    {
        return $this->items[$offset];
    }

    /**
     * @param int|string $offset
     * @param ModelInterface $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof ModelInterface)) {
            throw new InvalidArgumentException('trying to store a non-model value');
        }
        $this->items[$offset] = $value;
    }

    /**
     * @param int|string $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * @return ArrayIterator<int|string, ModelInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param callable $func
     * @return $this
     */
    public function each(callable $func): CollectionInterface
    {
        foreach ($this->items as &$item) {
            $item = $func($item);
        }

        return $this;
    }

    /**
     * @param callable $compare
     * @return $this
     */
    public function filter(callable $compare): CollectionInterface
    {
        foreach ($this->items as $key => $item) {
            if (!$compare($item)) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    /**
     * @param callable $compare
     * @return ModelInterface|null
     */
    public function find(callable $compare): ?ModelInterface
    {
        foreach ($this->items as $item) {
            if ($compare($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->items as $item) {
            if (is_callable($this->identifier)) {
                $result[call_user_func($this->identifier, $item)] = $item->jsonSerialize();
                continue;
            }
            $result[] = $item->jsonSerialize();
        }

        return $result;
    }
}
