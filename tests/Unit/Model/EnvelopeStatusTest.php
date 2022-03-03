<?php

namespace Hippy\Tests\Unit\Model;

use Hippy\Error\ErrorCollection;
use Hippy\Model\EnvelopeStatus;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Model\EnvelopeStatus */
class EnvelopeStatusTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::isSuccess
     * @covers ::getErrors
     */
    public function testConstruct(): void
    {
        $errors = $this->createMock(ErrorCollection::class);

        $sut = new EnvelopeStatus(true, $errors);

        $this->assertTrue($sut->isSuccess());
        $this->assertEquals($errors, $sut->getErrors());
    }
}
