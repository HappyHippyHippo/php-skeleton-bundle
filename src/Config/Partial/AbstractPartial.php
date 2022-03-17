<?php

namespace Hippy\Config\Partial;

use Hippy\Model\Model;
use RuntimeException;

/**
 * @method string getDomain()
 */
abstract class AbstractPartial extends Model implements PartialInterface
{
    /** @var string  */
    protected const ENV_PREFIX = 'HIPPY.';

    /** @var array<int|string, mixed> */
    protected array $config = [];

    /** @var array<int|string, mixed> */
    protected array $def = [];

    /**
     * @param string $domain
     */
    public function __construct(protected string $domain)
    {
        parent::__construct();
    }

    /**
     * @param string $path
     * @return bool
     */
    public function supports(string $path): bool
    {
        return str_starts_with($path, $this->domain . '.')
            && array_key_exists($path, $this->config);
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function get(string $path): mixed
    {
        return $this->config[$path] ?? null;
    }

    /**
     * @param string $path
     * @return bool|null
     */
    public function bool(string $path): ?bool
    {
        $value = $this->get($path);
        if (!is_null($value) && !is_bool($value)) {
            throw new RuntimeException("retrieving a non boolean config value");
        }
        return $value;
    }

    /**
     * @param string $path
     * @return int|null
     */
    public function int(string $path): ?int
    {
        $value = $this->get($path);
        if (!is_null($value) && !is_int($value)) {
            throw new RuntimeException("retrieving a non integer config value");
        }
        return $value;
    }

    /**
     * @param string $path
     * @return string|null
     */
    public function string(string $path): ?string
    {
        $value = $this->get($path);
        if (!is_null($value) && !is_string($value)) {
            throw new RuntimeException("retrieving a non string config value");
        }
        return $value;
    }

    /**
     * @param string $path
     * @return array<int|string, mixed>|null
     */
    public function array(string $path): ?array
    {
        $value = $this->get($path);
        if (!is_null($value) && !is_array($value)) {
            throw new RuntimeException("retrieving a non array config value");
        }
        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $result = [];

        /** @var array<string, mixed> $config */
        $config = parent::jsonSerialize()['config'];
        foreach ($config as $path => $value) {
            $result[substr($path, strlen($this->domain) + 1)] = $value;
        }

        return $result;
    }

    /**
     * @param string $path
     * @param string $type
     * @param array<string, mixed> $config
     * @return void
     */
    protected function loadType(string $path, string $type, array $config = []): void
    {
        $func = 'parse' . ucfirst($type);
        $this->config = array_merge(
            $this->config,
            [$path => $this->$func($config, $path, $this->def[$path] ?? null)]
        );
    }

    /**
     * @param array<string, mixed> $config
     * @param string $path
     * @param bool|null $default
     * @return bool|null
     */
    protected function parseBool(array $config, string $path, ?bool $default = null): ?bool
    {
        $env = str_replace('.', '_', strtoupper(self::ENV_PREFIX . $path));
        if ($envValue = getenv($env)) {
            return 'true' === $envValue || '1' === $envValue;
        }

        $value = $this->parse($config, $path, $default);
        if (!is_bool($value)) {
            throw new RuntimeException('config value is not a boolean');
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $config
     * @param string $path
     * @param int|null $default
     * @return int|null
     */
    protected function parseInt(array $config, string $path, ?int $default = null): ?int
    {
        $env = str_replace('.', '_', strtoupper(self::ENV_PREFIX . $path));
        if ($envValue = getenv($env)) {
            return (int) $envValue;
        }

        $value = $this->parse($config, $path, $default);
        if (!is_int($value)) {
            throw new RuntimeException('config value is not an integer');
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $config
     * @param string $path
     * @param string|null $default
     * @return string|null
     */
    protected function parseString(array $config, string $path, ?string $default = null): ?string
    {
        $env = str_replace('.', '_', strtoupper(self::ENV_PREFIX . $path));
        if ($envValue = getenv($env)) {
            return $envValue;
        }

        $value = $this->parse($config, $path, $default);
        if (!is_string($value)) {
            throw new RuntimeException('config value is not a string');
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $config
     * @param string $path
     * @param array<int|string, mixed> $default
     * @return array<int, string>|null
     */
    protected function parseArray(array $config, string $path, ?array $default = null): ?array
    {
        $env = str_replace('.', '_', strtoupper(self::ENV_PREFIX . $path));
        if ($envValue = getenv($env)) {
            return explode(',', $envValue);
        }

        $value = $this->parse($config, $path, $default);
        if (!is_array($value)) {
            throw new RuntimeException('config value is not an array');
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $config
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    private function parse(array $config, string $path, mixed $default = null): mixed
    {
        $elem = $config;
        foreach (explode('.', $path) as $step) {
            if (!is_array($elem) || !isset($elem[$step])) {
                $elem = null;
                break;
            }
            $elem = $elem[$step];
        }

        return $elem ?? $default;
    }
}
