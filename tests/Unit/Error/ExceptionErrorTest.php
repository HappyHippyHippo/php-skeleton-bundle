<?php

namespace Hippy\Tests\Unit\Error;

use Hippy\Error\ExceptionError;
use Exception;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Error\ExceptionError */
class ExceptionErrorTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $code = 123;
        $message = '__dummy_message__';
        $exception = new Exception($message, $code);

        $sut = new ExceptionError($code, $message, $exception);

        $this->assertEquals($code, $sut->getCode());
        $this->assertEquals($message, $sut->getMessage());
        $this->assertEquals($exception->getFile(), $sut->getFile());
        $this->assertEquals($exception->getLine(), $sut->getLine());
        $this->assertEquals($exception->getTrace(), $sut->getTrace());
    }
}
