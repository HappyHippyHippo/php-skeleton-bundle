<?php

namespace Hippy\Model;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * @method Model[] getItems()
 * @method callable|null getIdentifier()
 * @method Collection setIdentifier(callable|null $identifier)
 * @implements IteratorAggregate<int, Model>
 * @implements ArrayAccess<int, Model>
 */
abstract class Collection extends Model implements Countable, IteratorAggregate, ArrayAccess
{
    /** @var Model[] */
    protected array $items = [];

    /** @var callable|null */
    protected mixed $identifier = null;

    /**
     * @param string $type
     * @param Model[] $items
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected string $type,
        array $items = []
    ) {
        parent::__construct();

        foreach ($items as $item) {
            $this->add($item);
        }
        $this->identifier = null;
    }

    /**
     * @param Model $item
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add(Model $item): self
    {
        if (!($item instanceof $this->type)) {
            throw new InvalidArgumentException(sprintf('invalid %s item type', $this->type));
        }

        $this->items[] = $item;
        return $this;
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
     * @return Model
     */
    public function offsetGet(mixed $offset): Model
    {
        return $this->items[$offset];
    }

    /**
     * @param int|string $offset
     * @param Model $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof Model)) {
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
     * @return ArrayIterator<int|string, Model>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param callable $func
     * @return $this
     */
    public function each(callable $func): Collection
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
    public function filter(callable $compare): Collection
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
     * @return Model|null
     */
    public function find(callable $compare): ?Model
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
