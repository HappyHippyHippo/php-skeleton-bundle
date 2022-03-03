<?php

namespace HHH\Tests\Unit\Model;

use HHH\Error\ErrorCollection;
use HHH\Model\EnvelopeStatus;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \HHH\Model\EnvelopeStatus */
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
