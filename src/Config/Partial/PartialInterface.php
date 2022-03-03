<?php

namespace Hippy\Config\Partial;

use Hippy\Model\ModelInterface;

interface PartialInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * @param string $path
     * @return bool
     */
    public function supports(string $path): bool;

    /**
     * @param string $path
     * @return mixed
     */
    public function get(string $path): mixed;

    /**
     * @param array<string, mixed> $config
     * @return PartialInterface
     */
    public function load(array $config = []): PartialInterface;
}
