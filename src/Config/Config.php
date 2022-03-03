<?php

namespace Hippy\Config;

use Hippy\Config\Partial\PartialInterface;
use Hippy\Model\Model;

class Config extends Model implements ConfigInterface
{
    /** @var PartialInterface[] */
    protected array $partials = [];

    /**
     * @param iterable<PartialInterface> $partials
     * @param array<int|string, mixed>   $config
     */
    public function __construct(protected string $root = '', iterable $partials = [], array $config = [])
    {
        parent::__construct();

        $config = array_reduce($config, function (array $carry, mixed $config): array {
            if (is_array($config)) {
                return array_merge($carry, $config);
            }
            return $carry;
        }, []);

        foreach ($partials as $partial) {
            if ($partial instanceof PartialInterface) {
                $this->partials[$partial->getDomain()] = $partial->load($config);
            }
        }
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function get(string $path): mixed
    {
        foreach ($this->partials as $partial) {
            if ($partial->supports($path)) {
                return $partial->get($path);
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->partials as $domain => $partial) {
            $result[$domain] = $partial->jsonSerialize();
        }

        return $result;
    }
}
