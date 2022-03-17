<?php

namespace Hippy\Config\Partial;

interface PartialInterface
{
    /**
     * @param array<string, mixed> $config
     * @return $this
     */
    public function load(array $config = []): self;

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
}
