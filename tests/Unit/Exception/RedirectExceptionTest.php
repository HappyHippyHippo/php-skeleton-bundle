<?php

namespace Hippy\Tests\Unit\Exception;

use Hippy\Exception\Exception;
use Hippy\Exception\RedirectException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Exception\RedirectException */
class RedirectExceptionTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getURL
     */
    public function testConstructor(): void
    {
        $url = '__dummy_url__';
        $status = Response::HTTP_UNAUTHORIZED;
        $message = '__dummy_message__';
        $previous = new Exception();
        $headers = ['__dummy_header__' => '__dummy_header_value__'];
        $code = 123;

        $sut = new RedirectException($url, $status, $message, $previous, $headers, $code);

        $this->assertEquals($url, $sut->getURL());
        $this->assertEquals($status, $sut->getStatusCode());
        $this->assertEquals($message, $sut->getMessage());
        $this->assertSame($previous, $sut->getPrevious());
        $this->assertEquals($headers, $sut->getHeaders());
        $this->assertEquals($code, $sut->getCode());
    }
}
