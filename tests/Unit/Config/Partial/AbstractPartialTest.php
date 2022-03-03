<?php

namespace Hippy\Tests\Unit\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;

/** @coversDefaultClass \Hippy\Config\Partial\AbstractPartial */
class AbstractPartialTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getDomain
     */
    public function testGetDomain(): void
    {
        $domain = '__dummy_domain__';
        $sut = $this->create($domain);

        $this->assertEquals($domain, $sut->getDomain());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::supports
     */
    public function testSupportsDiscardsNonDomainPath(): void
    {
        $domain = '__dummy_domain__';
        $path = '__dummy_other_domain__.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$domain . '.__dummy_path__' => $value]);

        $this->assertFalse($sut->supports($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::supports
     */
    public function testSupportsDiscardsNonExistingPath(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_other_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$domain . '.__dummy_path__' => $value]);

        $this->assertFalse($sut->supports($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::__construct
     */
    public function testSupportsReturnTrueOnValidPath(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->assertTrue($sut->supports($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::get
     */
    public function testGetReturnNullIfPathDontExists(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->assertNull($sut->get($domain . '.__dummy_other_path__'));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::get
     */
    public function testGetReturnStoredValue(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->assertEquals($value, $sut->get($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::bool
     */
    public function testBoolReturnNullIfPathDontExists(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->assertNull($sut->bool($domain . '.__dummy_other_path__'));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::bool
     */
    public function testBoolReturnStoredValue(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $sut = $this->create($domain, [$path => true]);

        $this->assertTrue($sut->bool($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::bool
     */
    public function testBoolThrowsIfValuesIsNotBoolean(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('retrieving a non boolean config value');
        $sut->bool($path);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::int
     */
    public function testIntReturnNullIfPathDontExists(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->assertNull($sut->int($domain . '.__dummy_other_path__'));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::int
     */
    public function testIntReturnStoredValue(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = 123;
        $sut = $this->create($domain, [$path => $value]);

        $this->assertEquals($value, $sut->int($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::int
     */
    public function testIntThrowsIfValuesIsNotBoolean(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('retrieving a non integer config value');
        $sut->int($path);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::string
     */
    public function testStringReturnNullIfPathDontExists(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->assertNull($sut->string($domain . '.__dummy_other_path__'));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::string
     */
    public function testStringReturnStoredValue(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->assertEquals($value, $sut->string($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::string
     */
    public function testStringThrowsIfValuesIsNotBoolean(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = 123;
        $sut = $this->create($domain, [$path => $value]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('retrieving a non string config value');
        $sut->string($path);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::array
     */
    public function testArrayReturnNullIfPathDontExists(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->assertNull($sut->array($domain . '.__dummy_other_path__'));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::array
     */
    public function testArrayReturnStoredValue(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = ['__dummy_value__'];
        $sut = $this->create($domain, [$path => $value]);

        $this->assertEquals($value, $sut->array($path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::array
     */
    public function testSArrayThrowsIfValuesIsNotBoolean(): void
    {
        $domain = '__dummy_domain__';
        $path = $domain . '.__dummy_path__';
        $value = '__dummy_value__';
        $sut = $this->create($domain, [$path => $value]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('retrieving a non array config value');
        $sut->array($path);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $domain = '__dummy_domain__';
        $config = [
            '__dummy_domain__.__dummy_field1__' => '__dummy_value1__',
            '__dummy_domain__.__dummy_field2__' => '__dummy_value2__',
            '__dummy_domain__.__dummy_field3__' => '__dummy_value3__',
        ];
        $expected = [
            '__dummy_field1__' => '__dummy_value1__',
            '__dummy_field2__' => '__dummy_value2__',
            '__dummy_field3__' => '__dummy_value3__',
        ];

        $sut = $this->create($domain, $config);

        $this->assertEquals($expected, $sut->jsonSerialize());
    }

    /**
     * @param string $domain
     * @param array<int|string, mixed> $config
     * @return AbstractPartial
     */
    private function create(string $domain, array $config = []): AbstractPartial
    {
        $sut = $this->getMockForAbstractClass(AbstractPartial::class, [$domain]);

        $prop = new ReflectionProperty(AbstractPartial::class, 'config');
        $prop->setValue($sut, $config);

        return $sut;
    }
}
