<?php

namespace Hippy\Tests\Unit\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/** @coversDefaultClass \Hippy\Config\Partial\AbstractPartial */
class LoadTypeTest extends TestCase
{
    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseBool
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeBoolFromDefault(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function __construct(string $domain)
            {
                parent::__construct($domain);
                $this->def = ['domain.field' => true];
            }
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'bool');
        $this->assertTrue($sut->get($path));
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseBool
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeBoolFromConfig(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'bool', ['domain' => ['field' => true]]);
        $this->assertTrue($sut->get($path));
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseBool
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeBoolFromConfigThrowIfConfigIsNotBoolean(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("config value is not a boolean");

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'bool', ['domain' => ['field' => '__dummy_value__']]);
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseBool
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeBoolFromEnvironment(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        putenv('HIPPY_DOMAIN_FIELD=1');
        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'bool', ['domain' => ['field' => false]]);
        $this->assertTrue($sut->get($path));
        putenv('HIPPY_DOMAIN_FIELD=');
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseInt
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeIntFromDefault(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function __construct(string $domain)
            {
                parent::__construct($domain);
                $this->def = ['domain.field' => 123];
            }
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'int');
        $this->assertEquals(123, $sut->get($path));
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseInt
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeIntFromConfig(): void
    {
        $value = 123;
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'int', ['domain' => ['field' => $value]]);
        $this->assertEquals($value, $sut->get($path));
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseInt
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeIntFromConfigThrowIfConfigIsNotInt(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("config value is not an integer");

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'int', ['domain' => ['field' => '__dummy_value__']]);
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseInt
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeIntFromConfigFromEnvironment(): void
    {
        $value = 123;
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        putenv('HIPPY_DOMAIN_FIELD=' . $value);
        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'int', ['domain' => ['field' => 321]]);
        $this->assertEquals($value, $sut->get($path));
        putenv('HIPPY_DOMAIN_FIELD=');
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseString
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeStringFromDefault(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function __construct(string $domain)
            {
                parent::__construct($domain);
                $this->def = ['domain.field' => '__dummy_value__'];
            }
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'string');
        $this->assertEquals('__dummy_value__', $sut->get($path));
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseString
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeStringFromConfig(): void
    {
        $value = '__dummy_value__';
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'string', ['domain' => ['field' => $value]]);
        $this->assertEquals($value, $sut->get($path));
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseString
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeStringFromConfigThrowIfConfigInNotString(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("config value is not a string");

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'string', ['domain' => ['field' => 123]]);
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseString
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeStringFromEnvironment(): void
    {
        $value = '__dummy_value__';
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        putenv('HIPPY_DOMAIN_FIELD=' . $value);
        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'string', ['domain' => ['field' => '__dummy_other_value__']]);
        $this->assertEquals($value, $sut->get($path));
        putenv('HIPPY_DOMAIN_FIELD=');
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseArray
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeArrayFromDefault(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function __construct(string $domain)
            {
                parent::__construct($domain);
                $this->def = ['domain.field' => ['__dummy_value__']];
            }
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'array');
        $this->assertEquals(['__dummy_value__'], $sut->get($path));
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseArray
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeArrayFromConfig(): void
    {
        $value = ['__dummy_value__'];
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'array', ['domain' => ['field' => $value]]);
        $this->assertEquals($value, $sut->get($path));
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseArray
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeArrayFromConfigThrowIfConfigInNotArray(): void
    {
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("config value is not an array");

        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'array', ['domain' => ['field' => '__dummy_value__']]);
    }

    /**
     * @return void
     * @covers ::loadType
     * @covers ::parseArray
     * @covers ::parse
     * @covers ::get
     * @throws ReflectionException
     */
    public function testLoadTypeArrayFromEnvironment(): void
    {
        $value = ['__dummy_value__'];
        $path = 'domain.field';
        $sut = new class ('domain') extends AbstractPartial {
            public function load(array $config = []): AbstractPartial
            {
                return $this;
            }
        };

        putenv('HIPPY_DOMAIN_FIELD=' . $value[0]);
        $method = new ReflectionMethod(AbstractPartial::class, 'loadType');
        $method->invoke($sut, $path, 'array', ['domain' => ['field' => '__dummy_other_value__']]);
        $this->assertEquals($value, $sut->get($path));
        putenv('HIPPY_DOMAIN_FIELD=');
    }
}
