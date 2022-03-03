<?php

namespace Hippy\Tests\Unit\Command;

use Hippy\Command\CommandException;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Command\CommandException */
class CommandExceptionTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getMessageType
     * @covers ::getPreMessageAction
     * @covers ::getPostMessageAction
     */
    public function testConstructor(): void
    {
        $message = '__dummy_message__';
        $messageType = '__dummy_message_type__';
        $preMessageAction = function () {
        };
        $postMessageAction = function () {
        };

        $sut = new CommandException($message, $messageType, $preMessageAction, $postMessageAction);

        $this->assertEquals($message, $sut->getMessage());
        $this->assertEquals($messageType, $sut->getMessageType());
        $this->assertEquals($preMessageAction, $sut->getPreMessageAction());
        $this->assertEquals($postMessageAction, $sut->getPostMessageAction());
    }
}
