<?php

namespace HHH\Tests\Unit\Model;

use DateTime;
use HHH\Model\Model;
use HHH\Model\ModelInterface;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

/** @coversDefaultClass \HHH\Model\Model */
class ModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $values = ['field' => '__dummy_value__'];
        $model = new class ($values) extends Model {
            protected string $field;

            public function getField(): string
            {
                return $this->field;
            }
        };

        $this->assertEquals($values['field'], $model->getField());
    }

    /**
     * @return void
     * @covers ::set
     */
    public function testSet(): void
    {
        $values = ['field' => '__dummy_value__'];
        $model = new class () extends Model {
            protected string $field;

            public function getField(): string
            {
                return $this->field;
            }
        };

        $model->set($values);
        $this->assertEquals($values['field'], $model->getField());
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::addParser
     */
    public function testAddParser(): void
    {
        $model = $this->getMockForAbstractClass(Model::class);
        $name = '__dummy_name__';
        $callable = function () {
        };

        $addParser = new ReflectionMethod(Model::class, 'addParser');
        $this->assertSame($model, $addParser->invoke($model, $name, $callable));

        $parsers = new ReflectionProperty(Model::class, 'parsers');
        $this->assertEquals([$name => $callable], $parsers->getValue($model));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::addHideParser
     */
    public function testAddHideParser(): void
    {
        $model = new class () extends Model {
            public string $field;
        };
        $model->field = '__dummy_value__';

        $addParser = new ReflectionMethod(Model::class, 'addHideParser');
        $this->assertSame($model, $addParser->invoke($model, 'field'));

        $json = $model->jsonSerialize();
        $this->assertArrayNotHasKey('field', $json);
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::addObfuscateParser
     */
    public function testAddObfuscateParser(): void
    {
        $model = new class () extends Model {
            public string $field;
        };
        $model->field = '__dummy_plain_text__';

        $addParser = new ReflectionMethod(Model::class, 'addObfuscateParser');
        $this->assertSame($model, $addParser->invoke($model, 'field'));

        $json = $model->jsonSerialize();
        $this->assertArrayHasKey('field', $json);
        $this->assertEquals('******', $json['field']);
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::addDateTimeParser
     */
    public function testAddDateTimeParser(): void
    {
        $date = new DateTime('2000-01-01');
        $model = new class () extends Model {
            public ?DateTime $field;
        };
        $model->field = $date;

        $addParser = new ReflectionMethod(Model::class, 'addDateTimeParser');
        $this->assertSame($model, $addParser->invoke($model, 'field'));

        $json = $model->jsonSerialize();
        $this->assertArrayHasKey('field', $json);
        $this->assertEquals($date->format('Y-m-d H:i:s'), $json['field']);
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::toDateTime
     */
    public function testToDateTimeWithNullValue(): void
    {
        $expected = null;

        $model = $this->getMockForAbstractClass(Model::class);

        $toDateTime = new ReflectionMethod(Model::class, 'toDateTime');
        $this->assertEquals($expected, $toDateTime->invoke($model, null));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::toDateTime
     */
    public function testToDateTime(): void
    {
        $value = '2020-01-01 01:01:01';
        $expected = new DateTime($value);

        $model = $this->getMockForAbstractClass(Model::class);

        $toDateTime = new ReflectionMethod(Model::class, 'toDateTime');
        $this->assertEquals($expected, $toDateTime->invoke($model, $value));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::toDateTime
     */
    public function testToDateTimeWithInvalidDate(): void
    {
        $value = '__invalid_date__';

        $model = $this->getMockForAbstractClass(Model::class);

        $toDateTime = new ReflectionMethod(Model::class, 'toDateTime');
        $this->assertNull($toDateTime->invoke($model, $value));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::fromDateTime
     */
    public function testFromDateTime(): void
    {
        $expected = '2020-01-01 01:01:01';
        $format = 'Y-m-d H:i:s';
        $value = new DateTime($expected);

        $model = $this->getMockForAbstractClass(Model::class);

        $fromDateTime = new ReflectionMethod(Model::class, 'fromDateTime');
        $this->assertEquals($expected, $fromDateTime->invoke($model, $value, $format));
    }

    /**
     * @param ModelInterface $model
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::jsonSerialize
     * @dataProvider providerForJsonSerializeTests
     */
    public function testJsonSerialize(ModelInterface $model, array $expected): void
    {
        $this->assertEquals($expected, $model->jsonSerialize());
    }

    /**
     * @return array<string, mixed>
     * @throws ReflectionException
     */
    public function providerForJsonSerializeTests(): array
    {
        $creator = function (array $fields = [], array $parsers = []): ModelInterface {
            $model = $this->getMockForAbstractClass(Model::class);
            foreach ($fields as $field => $value) {
                $model->$field = $value;
            }
            foreach ($parsers as $field => $callback) {
                $method = new ReflectionMethod(Model::class, 'addParser');
                $method->invoke($model, $field, $callback);
            }

            return $model;
        };

        return [
            'empty model' => [
                'model' => $creator(),
                'expected' => [],
            ],
            'model with simple fields' => [
                'model' => $creator(['field1' => true, 'field2' => 123, 'field3' => '__dummy_message__']),
                'expected' => [
                    'field1' => true,
                    'field2' => 123,
                    'field3' => '__dummy_message__',
                ],
            ],
            'model with inner serializable' => [
                'model' => $creator(['outerField' => new class () extends Model {
                    protected int $innerField = 123;
                }]),
                'expected' => [
                    'outerField' => [
                        'innerField' => 123,
                    ],
                ],
            ],
            'model with parser' => [
                'model' => $creator(['field' => 123], ['field' => function ($val) {
                    return $val * 100;
                }]),
                'expected' => [
                    'field' => 12300,
                ],
            ],
            'model with null field' => [
                'model' => $creator(['field' => null]),
                'expected' => [],
            ],
        ];
    }
}
