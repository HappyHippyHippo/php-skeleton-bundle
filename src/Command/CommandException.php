<?php

namespace Hippy\Command;

use Closure;
use Exception;

class CommandException extends Exception
{
    /**
     * @param string $message
     * @param string $messageType
     * @param Closure|null $preMessageAction
     * @param Closure|null $postMessageAction
     */
    public function __construct(
        string $message,
        protected string $messageType,
        protected ?Closure $preMessageAction = null,
        protected ?Closure $postMessageAction = null
    ) {
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getMessageType(): string
    {
        return $this->messageType;
    }

    /**
     * @return Closure|null
     */
    public function getPreMessageAction(): ?Closure
    {
        return $this->preMessageAction;
    }

    /**
     * @return Closure|null
     */
    public function getPostMessageAction(): ?Closure
    {
        return $this->postMessageAction;
    }
}
