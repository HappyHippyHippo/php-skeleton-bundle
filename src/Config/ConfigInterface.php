<?php

namespace HHH\Config;

use HHH\Model\ModelInterface;

interface ConfigInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getRoot(): string;

    /**
     * @param string $path
     * @return mixed
     */
    public function get(string $path): mixed;
}
