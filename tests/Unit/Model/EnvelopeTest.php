<?php

namespace Hippy\Tests\Unit\Model;

use Hippy\Error\ErrorCollection;
use Hippy\Model\Envelope;
use Hippy\Model\ModelInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Model\Envelope */
class EnvelopeTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getStatus
     * @covers ::getData
     */
    public function testConstructWithoutErrorCollection(): void
    {
        $envelope = new Envelope();

        $this->assertTrue($envelope->getStatus()->isSuccess());
        $this->assertSame(0, $envelope->getStatus()->getErrors()->count());
        $this->assertNull($envelope->getData());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getStatus
     * @covers ::getData
     */
    public function testConstructWithEmptyErrorCollection(): void
    {
        $errors = $this->createMock(ErrorCollection::class);
        $errors->expects($this->once())->method('count')->willReturn(0);

        $envelope = new Envelope($errors);

        $this->assertTrue($envelope->getStatus()->isSuccess());
        $this->assertSame($errors, $envelope->getStatus()->getErrors());
        $this->assertNull($envelope->getData());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getStatus
     * @covers ::getData
     */
    public function testConstructWithNonEmptyErrorCollection(): void
    {
        $errors = $this->createMock(ErrorCollection::class);
        $errors->expects($this->once())->method('count')->willReturn(1);

        $envelope = new Envelope($errors);

        $this->assertFalse($envelope->getStatus()->isSuccess());
        $this->assertSame($errors, $envelope->getStatus()->getErrors());
        $this->assertNull($envelope->getData());
    }

    /**
     * @return void
     * @covers ::setData
     */
    public function testSetData(): void
    {
        $data = $this->createMock(ModelInterface::class);

        $envelope = new Envelope();

        $this->assertSame($envelope, $envelope->setData($data));
        $this->assertSame($data, $envelope->getData());
    }
}
