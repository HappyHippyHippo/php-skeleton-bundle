<?php

namespace Hippy\Tests\Unit\Error;

use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Error\ErrorCollection */
class ErrorCollectionTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $collection = new ErrorCollection();

        $prop = new ReflectionProperty(ErrorCollection::class, 'type');
        $this->assertEquals(Error::class, $prop->getValue($collection));
    }
}
