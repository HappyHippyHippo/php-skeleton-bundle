<?php

namespace HHH\Tests\Unit\Error;

use HHH\Error\Error;
use HHH\Error\ErrorCollection;
use HHH\Error\ErrorInterface;
use HHH\Model\ModelInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \HHH\Error\ErrorCollection */
class ErrorCollectionTest extends TestCase
{
    /**
     * @return void
     * @covers ::add
     */
    public function testAddThrowsIfArgumentIsNotErrorInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $error = $this->createMock(ModelInterface::class);

        $collection = new ErrorCollection();
        $collection->add($error);
    }

    /**
     * @return void
     * @covers ::add
     */
    public function testAdd(): void
    {
        $error = $this->createMock(ErrorInterface::class);

        $collection = new ErrorCollection();
        $collection->add($error);
        $this->assertEquals([$error], $this->getItems($collection));
    }

    /**
     * @param ErrorCollection $collection
     * @return Error[]
     */
    private function getItems(ErrorCollection $collection): array
    {
        $prop = new ReflectionProperty(ErrorCollection::class, 'items');
        $items = $prop->getValue($collection);
        if (!is_array($items)) {
            $this->fail("collection items are not an array");
        }
        return $items;
    }
}
