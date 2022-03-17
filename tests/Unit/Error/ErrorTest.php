<?php

namespace Hippy\Tests\Unit\Error;

use Hippy\Error\Error;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/** @coversDefaultClass \Hippy\Error\Error */
class ErrorTest extends TestCase
{
    /**
     * @param string $code
     * @param string $message
     * @param string[] $codes
     * @param string[] $expected
     * @return void
     * @covers ::__construct
     * @dataProvider getProvider
     */
    public function testConstructor(string $code, string $message, array $codes, array $expected): void
    {
        $sut = new Error($code, $message);

        $this->assertEquals($expected['service'], $sut->getService());
        $this->assertEquals($expected['endpoint'], $sut->getEndpoint());
        $this->assertEquals($expected['param'], $sut->getParam());
        $this->assertEquals($expected['code'], $sut->getCode());
        $this->assertEquals($expected['message'], $sut->getMessage());

        $sut->setService($codes['service']);
        $sut->setEndpoint($codes['endpoint']);
        $sut->setParam($codes['param']);
        $sut->setCode($codes['code']);

        $this->assertEquals($expected['json'], $sut->jsonSerialize());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getService
     * @covers ::setService
     */
    public function testServiceGetterAndSetter(): void
    {
        $code = '__dummy_code__';
        $sut = new Error();

        $this->assertEquals('', $sut->getService());
        $this->assertSame($sut, $sut->setService($code));
        $this->assertEquals($code, $sut->getService());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getEndpoint
     * @covers ::setEndpoint
     */
    public function testEndpointGetterAndSetter(): void
    {
        $code = '__dummy_code__';
        $sut = new Error();

        $this->assertEquals('', $sut->getEndpoint());
        $this->assertSame($sut, $sut->setEndpoint($code));
        $this->assertEquals($code, $sut->getEndpoint());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getParam
     * @covers ::setParam
     */
    public function testParamGetterAndSetter(): void
    {
        $code = '__dummy_code__';
        $sut = new Error();

        $this->assertEquals('', $sut->getParam());
        $this->assertSame($sut, $sut->setParam($code));
        $this->assertEquals($code, $sut->getParam());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getCode
     * @covers ::setCode
     */
    public function testCodeGetterAndSetter(): void
    {
        $code = '__dummy_code__';
        $sut = new Error();

        $this->assertEquals('', $sut->getCode());
        $this->assertSame($sut, $sut->setCode($code));
        $this->assertEquals($code, $sut->getCode());
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testMessageGetterAndSetter(): void
    {
        $message = '__dummy_message__';
        $sut = new Error();

        $this->assertEquals('', $sut->getMessage());
        $this->assertSame($sut, $sut->setMessage($message));
        $this->assertEquals($message, $sut->getMessage());
    }

    /**
     * @param string $provider
     * @return array<string, mixed>
     */
    public function getProvider(string $provider): array
    {
        $providers = Yaml::parseFile(sprintf('%s/%s.provider.yaml', dirname(__FILE__), basename(__FILE__, '.php')));
        if (!is_array($providers) || !isset($providers[$provider]) || !is_array($providers[$provider])) {
            $this->fail("invalid provider");
        }

        return $providers[$provider];
    }
}
