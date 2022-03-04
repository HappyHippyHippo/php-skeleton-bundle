<?php

namespace Hippy\Model;

use BadMethodCallException;
use DateTime;
use Exception;
use JsonSerializable;
use ReflectionException;
use ReflectionProperty;

abstract class Model implements ModelInterface
{
    /** @var callable[] */
    private array $parsers = [];

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values = [])
    {
        if (count($values)) {
            $this->set($values);
        }
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $arguments
     * @return mixed
     * @throws BadMethodCallException
     * @throws ReflectionException
     */
    public function __call(string $name, array $arguments): mixed
    {
        $type = function (string $field): string {
            $prop = new ReflectionProperty($this, $field);
            return (string) $prop->getType();
        };

        if (str_starts_with($name, 'is')) {
            $field = lcfirst(substr($name, 2));
            if (property_exists($this, $field) && $type($field) == 'bool') {
                return $this->$field;
            }
        } elseif (str_starts_with($name, 'get')) {
            $field = lcfirst(substr($name, 3));
            if (property_exists($this, $field) && $type($field) != 'bool') {
                return $this->$field;
            }
        } elseif (str_starts_with($name, 'set')) {
            $field = lcfirst(substr($name, 3));
            if (property_exists($this, $field)) {
                if (!isset($arguments[0])) {
                    throw new \InvalidArgumentException('missing set argument');
                }
                $this->$field = $arguments[0];
                return $this;
            }
        }

        throw new BadMethodCallException();
    }

    /**
     * @param array<string, mixed> $values
     * @return $this
     */
    public function set(array $values): ModelInterface
    {
        foreach ($values as $key => $value) {
            if ($key != 'parsers' && property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $result = [];
        foreach (get_object_vars($this) as $name => $value) {
            if ('parsers' == $name) {
                continue;
            }

            if (array_key_exists($name, $this->parsers)) {
                $value = $this->parsers[$name]($value);
            }

            if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            }

            if (null !== $value) {
                $result[$name] = $value;
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @param callable $parser
     * @return $this
     */
    protected function addParser(string $name, callable $parser): ModelInterface
    {
        $this->parsers[$name] = $parser;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function addHideParser(string $name): ModelInterface
    {
        $this->parsers[$name] = function () {
            return null;
        };

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function addObfuscateParser(string $name): ModelInterface
    {
        $this->parsers[$name] = function () {
            return '******';
        };

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function addDateTimeParser(string $name): ModelInterface
    {
        $this->parsers[$name] = function (?DateTime $date) {
            return $this->fromDateTime($date);
        };

        return $this;
    }

    /**
     * @param string|null $value
     * @return DateTime|null
     */
    protected function toDateTime(?string $value): ?DateTime
    {
        try {
            return $value ? new DateTime($value) : null;
        } catch (Exception) {
        }

        return null;
    }

    /**
     * @param DateTime|null $value
     * @param string|null $format
     * @return string|null
     */
    protected function fromDateTime(?DateTime $value, ?string $format = null): ?string
    {
        return $value?->format($format ?? 'Y-m-d H:i:s');
    }
}
