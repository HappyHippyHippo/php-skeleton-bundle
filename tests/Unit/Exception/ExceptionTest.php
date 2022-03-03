<?php

namespace Hippy\Tests\Unit\Exception;

use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Hippy\Exception\Exception;
use Hippy\Model\ModelInterface;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Exception\Exception */
class ExceptionTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorWithDefaultArguments(): void
    {
        $sut = new Exception();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $sut->getStatusCode());
        $this->assertEquals('', $sut->getMessage());
        $this->assertSame(null, $sut->getPrevious());
        $this->assertEquals([], $sut->getHeaders());
        $this->assertEquals(0, $sut->getCode());

        $property = new ReflectionProperty(Exception::class, 'errors');
        $errors = $property->getValue($sut);
        $this->assertInstanceOf(ErrorCollection::class, $errors);
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorWithoutDefaultArguments(): void
    {
        $status = Response::HTTP_UNAUTHORIZED;
        $message = '__dummy_message__';
        $previous = new Exception();
        $headers = ['__dummy_header__' => '__dummy_header_value__'];
        $code = 123;

        $sut = new Exception($status, $message, $previous, $headers, $code);

        $this->assertEquals($status, $sut->getStatusCode());
        $this->assertEquals($message, $sut->getMessage());
        $this->assertSame($previous, $sut->getPrevious());
        $this->assertEquals($headers, $sut->getHeaders());
        $this->assertEquals($code, $sut->getCode());

        $property = new ReflectionProperty(Exception::class, 'errors');
        $errors = $property->getValue($sut);
        $this->assertInstanceOf(ErrorCollection::class, $errors);
    }

    /**
     * @param Error[] $errors
     * @return void
     * @covers ::addError
     * @covers ::getErrors
     * @dataProvider providerForErrorsTests
     */
    public function testAddAndGetErrorFunctions(array $errors): void
    {
        $sut = new Exception();
        foreach ($errors as $error) {
            $sut->addError($error);
        }

        $storedErrors = $sut->getErrors();
        $this->assertEquals(count($errors), count($storedErrors));

        $serializedErrors = $storedErrors->jsonSerialize();
        foreach ($serializedErrors as $index => $serializedError) {
            $this->assertSame($errors[$index]->jsonSerialize(), $serializedError);
        }
    }

    /**
     * @param Error[] $errors
     * @return void
     * @covers ::addErrors
     * @dataProvider providerForErrorsTests
     */
    public function testAddErrors(array $errors): void
    {
        $collection = new ErrorCollection();
        foreach ($errors as $error) {
            $collection->add($error);
        }

        $sut = new Exception();
        $sut->addErrors($collection);

        $storedErrors = $sut->getErrors();
        $this->assertEquals(count($errors), count($storedErrors));

        $serializedErrors = $storedErrors->jsonSerialize();
        foreach ($serializedErrors as $index => $serializedError) {
            $this->assertSame($errors[$index]->jsonSerialize(), $serializedError);
        }
    }

    /**
     * @return void
     * @@covers ::getData
     * @@covers ::setData
     */
    public function testDataGetterAndSetter(): void
    {
        $data = $this->createMock(ModelInterface::class);

        $sut = new Exception();

        $this->assertNull($sut->getData());
        $this->assertSame($sut, $sut->setData($data));
        $this->assertSame($data, $sut->getData());
    }

    /**
     * @return array<string, mixed>
     */
    public function providerForErrorsTests(): array
    {
        return [
            'no errors' => [
                'errors' => [],
            ],
            'single error' => [
                'errors' => [
                    new Error(456, '__dummy_transformer_message__'),
                ],
            ],
            'alot of errors' => [
                'errors' => [
                    new Error(12, '__dummy_transformer_message_1__'),
                    new Error(34, '__dummy_transformer_message_2__'),
                    new Error(56, '__dummy_transformer_message_3__'),
                    new Error(78, '__dummy_transformer_message_4__'),
                    new Error(90, '__dummy_transformer_message_5__'),
                ],
            ],
        ];
    }
}
