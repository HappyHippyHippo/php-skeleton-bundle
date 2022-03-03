<?php

namespace Hippy\Error;

abstract class ErrorCode
{
    /** @var int */
    public const UNKNOWN = 0;

    /** @var string[] */
    public const ERROR_TO_MESSAGE = [
        self::UNKNOWN => 'Unexpected error',
    ];
}
