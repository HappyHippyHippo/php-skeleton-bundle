<?php

namespace Hippy\Tests\Unit\Config;

use Hippy\Config\Config;
use Hippy\Config\Partial\AbstractPartial;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Config\Config */
class ConfigTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $root = '__dummy_root__';
        $config1 = ['__dummy_config_1__'];
        $config2 = ['__dummy_config_2__'];
        $partial = $this->createMock(AbstractPartial::class);
        $partial
            ->expects($this->once())
            ->method('load')
            ->with(array_merge($config1, $config2))
            ->willReturn($partial);
        new Config($root, [$partial], [$config1, '__dummy_string__', $config2]);
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testGetRoot(): void
    {
        $root = '__dummy_root__';
        $config = ['__dummy_config__'];
        $partial = $this->createMock(AbstractPartial::class);
        $partial->expects($this->once())->method('load')->with($config)->willReturn($partial);
        $sut = new Config($root, [$partial], [$config]);

        $this->assertEquals($root, $sut->getRoot());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::get
     */
    public function testPartialRequestReturnNullOnNotFound(): void
    {
        $root = '__dummy_root__';
        $path = '__dummy_path__';
        $sut = new Config($root, []);

        $this->assertNull($sut->get($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::get
     */
    public function testPartialRequestReturnFoundedValue(): void
    {
        $root = '__dummy_root__';
        $domain = '__dummy_domain__';
        $path = '__dummy_path__';
        $value = 123;

        $partial = $this->getMockBuilder(AbstractPartial::class)
            ->onlyMethods(['load', 'supports', 'get'])
            ->addMethods(['getDomain'])
            ->disableOriginalConstructor()
            ->getMock();
        $partial->expects($this->once())->method('getDomain')->willReturn($domain);
        $partial->expects($this->once())->method('load')->willReturn($partial);
        $partial->expects($this->once())->method('supports')->with($path)->willReturn(true);
        $partial->expects($this->once())->method('get')->with($path)->willReturn($value);

        $sut = new Config($root, [$partial]);

        $this->assertEquals($value, $sut->get($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $root = '__dummy_root__';
        $config = ['__dummy_config__'];
        $domain = '__dummy_domain__';
        $json = ['__dummy_json__'];

        $partial = $this->getMockBuilder(AbstractPartial::class)
            ->onlyMethods(['load', 'jsonSerialize'])
            ->addMethods(['getDomain'])
            ->disableOriginalConstructor()
            ->getMock();
        $partial->expects($this->once())->method('getDomain')->willReturn($domain);
        $partial->expects($this->once())->method('load')->with($config)->willReturn($partial);
        $partial->expects($this->once())->method('jsonSerialize')->willReturn($json);

        $sut = new Config($root, [$partial], [$config]);

        $this->assertEquals([$domain => $json], $sut->jsonSerialize());
    }
}
