<?php

namespace Hippy\Tests\Unit\Model;

use ArrayIterator;
use Hippy\Model\Collection;
use Hippy\Model\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Model\Collection */
class CollectionTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructWithoutArgument(): void
    {
        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->assertEquals([], $collection->getItems());
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructWithArgument(): void
    {
        $item1 = $this->createMock(Model::class);
        $item2 = $this->createMock(Model::class);

        $collection = new class ([$item1, $item2]) extends Collection {
            public function add(Model $item): Collection
            {
                $this->items[] = $item;
                return $this;
            }
        };

        $this->assertEquals([$item1, $item2], $collection->getItems());
    }

    /**
     * @return void
     * @covers ::count
     */
    public function testCount(): void
    {
        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->assertEquals(0, $collection->count());

        $this->setItems($collection, ['__dummy_entry__']);
        $this->assertEquals(1, $collection->count());
    }

    /**
     * @return void
     * @covers ::offsetExists
     */
    public function testOffsetExists(): void
    {
        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->assertFalse($collection->offsetExists(0));
        $this->setItems($collection, ['__dummy_entry__']);
        $this->assertTrue($collection->offsetExists(0));
    }

    /**
     * @return void
     * @covers ::offsetGet
     */
    public function testOffsetGet(): void
    {
        $item = $this->createMock(Model::class);

        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->setItems($collection, [$item]);
        $this->assertEquals($item, $collection->offsetGet(0));
    }

    /**
     * @return void
     * @covers ::offsetSet
     */
    public function testOffsetSet(): void
    {
        $item = $this->createMock(Model::class);

        $collection = $this->getMockForAbstractClass(Collection::class);

        $collection->offsetSet(0, $item);
        $this->assertEquals($item, $collection->offsetGet(0));
    }

    /**
     * @return void
     * @covers ::offsetSet
     */
    public function testOffsetSetThrowsIfNotAModel(): void
    {
        $item = '__dummy_value__';

        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('trying to store a non-model value');

        // @phpstan-ignore-next-line
        $collection->offsetSet(0, $item);
    }

    /**
     * @return void
     * @covers ::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $item = $this->createMock(Model::class);

        $collection = $this->getMockForAbstractClass(Collection::class);

        $collection->offsetSet(0, $item);
        $this->assertTrue($collection->offsetExists(0));
        $collection->offsetUnset(0);
        $this->assertFalse($collection->offsetExists(0));
    }

    /**
     * @return void
     * @covers ::getIterator
     */
    public function testGetIterator(): void
    {
        $collection = $this->getMockForAbstractClass(Collection::class);

        $item1 = $this->createMock(Model::class);
        $item2 = $this->createMock(Model::class);
        $item3 = $this->createMock(Model::class);
        $list = [$item1, $item2, $item3];

        $this->setItems($collection, $list);

        $iterator = $collection->getIterator();
        $this->assertInstanceOf(ArrayIterator::class, $iterator);

        $check = [];
        foreach ($iterator as $item) {
            $check[] = $item;
        }

        $this->assertEquals($list, $check);
    }

    /**
     * @return void
     * @covers ::each
     */
    public function testEach(): void
    {
        $value = 1;
        $callCount = 0;
        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->setItems($collection, [$value]);
        $this->assertSame($collection, $collection->each(function ($value) use (&$callCount) {
            ++$callCount;

            return $value;
        }));

        $this->assertEquals(1, $callCount);
    }

    /**
     * @return void
     * @covers ::filter
     */
    public function testFilter(): void
    {
        $values = [1, 2, 3, 4];
        $expected = [1 => 2, 3 => 4];
        $callCount = 0;
        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->setItems($collection, $values);
        $this->assertSame($collection, $collection->filter(function ($value) use (&$callCount) {
            ++$callCount;

            return ($value % 2) == 0;
        }));

        $this->assertEquals(4, $callCount);
        $this->assertEquals($expected, $collection->getItems());
    }

    /**
     * @return void
     * @covers ::find
     */
    public function testFindSuccess(): void
    {
        $value1 = $this->createMock(Model::class);
        $value2 = $this->createMock(Model::class);
        $value3 = $this->createMock(Model::class);

        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->setItems($collection, [$value1, $value2, $value3]);
        $this->assertSame($value2, $collection->find(function ($value) use ($value2) {
            return $value === $value2;
        }));
    }

    /**
     * @return void
     * @covers ::find
     */
    public function testFindFail(): void
    {
        $value1 = $this->createMock(Model::class);
        $value2 = $this->createMock(Model::class);
        $value3 = $this->createMock(Model::class);

        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->setItems($collection, [$value1, $value2, $value3]);
        $this->assertNull($collection->find(function () {
            return false;
        }));
    }

    /**
     * @return void
     * @covers ::jsonSerialize
     */
    public function testJsonSerializeEmptyCollection(): void
    {
        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->assertEquals([], $collection->jsonSerialize());
    }

    /**
     * @return void
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $value1 = ['name' => '__dummy_name_1__', 'value' => '__dummy_value_1__'];
        $item1 = $this->createMock(Model::class);
        $item1->expects($this->once())->method('jsonSerialize')->willReturn($value1);

        $value2 = ['name' => '__dummy_name_2__', 'value' => '__dummy_value_2__'];
        $item2 = $this->createMock(Model::class);
        $item2->expects($this->once())->method('jsonSerialize')->willReturn($value2);

        $collection = $this->getMockForAbstractClass(Collection::class);

        $this->setItems($collection, [$item1, $item2]);
        $this->assertEquals([$value1, $value2], $collection->jsonSerialize());
    }

    /**
     * @return void
     * @covers ::jsonSerialize
     */
    public function testJsonSerializeWithIdentifier(): void
    {
        $value1 = ['name' => '__dummy_name_1__', 'value' => '__dummy_value_1__'];
        $item1 = $this->getMockBuilder(Model::class)
            ->addMethods(['getName'])
            ->onlyMethods(['jsonSerialize'])
            ->getMock();
        $item1->expects($this->once())->method('jsonSerialize')->willReturn($value1);
        $item1->expects($this->once())->method('getName')->willReturn($value1['name']);

        $value2 = ['name' => '__dummy_name_2__', 'value' => '__dummy_value_2__'];
        $item2 = $this->getMockBuilder(Model::class)
            ->addMethods(['getName'])
            ->onlyMethods(['jsonSerialize'])
            ->getMock();
        $item2->expects($this->once())->method('jsonSerialize')->willReturn($value2);
        $item2->expects($this->once())->method('getName')->willReturn($value2['name']);

        $collection = $this->getMockForAbstractClass(Collection::class);
        $collection->setIdentifier(function ($model): string {
            return $model->getName();
        });

        $this->setItems($collection, [$item1, $item2]);
        $this->assertEquals([
            '__dummy_name_1__' => $value1,
            '__dummy_name_2__' => $value2,
        ], $collection->jsonSerialize());
    }

    /**
     * @param Collection $collection
     * @param array<int|string, mixed> $items
     * @return void
     */
    private function setItems(Collection $collection, array $items): void
    {
        $prop = new ReflectionProperty(Collection::class, 'items');
        $prop->setValue($collection, $items);
    }
}
