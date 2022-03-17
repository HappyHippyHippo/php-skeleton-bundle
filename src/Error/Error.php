<?php

namespace Hippy\Error;

use Hippy\Model\Model;

/**
 * @method string getMessage()
 * @method Error setMessage(string $message)
 */
class Error extends Model
{
    /** @var array<int|string> */
    protected array $code;

    /**
     * @param int|string $code
     * @param string $message
     */
    public function __construct(int|string $code = '', protected string $message = '')
    {
        parent::__construct([
            'code' => ['service' => '', 'endpoint' => '', 'param' => '', 'code' => $code],
        ]);

        $this->addParser('code', function (array $code) {
            $result = [];
            foreach ($code as $key => $value) {
                if ('' !== $value) {
                    $result[] = is_numeric($value) ? sprintf('%s%s', $key[0], $value) : $value;
                }
            }

            return implode('.', $result);
        });
    }

    /**
     * @return int|string
     */
    public function getService(): int|string
    {
        return $this->code['service'];
    }

    /**
     * @param int|string $code
     * @return $this
     */
    public function setService(int|string $code): self
    {
        $this->code['service'] = $code;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getEndpoint(): int|string
    {
        return $this->code['endpoint'];
    }

    /**
     * @param int|string $code
c     */
    public function setEndpoint(int|string $code): self
    {
        $this->code['endpoint'] = $code;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getParam(): int|string
    {
        return $this->code['param'];
    }

    /**
     * @param int|string $code
     * @return $this
     */
    public function setParam(int|string $code): self
    {
        $this->code['param'] = $code;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getCode(): int|string
    {
        return $this->code['code'];
    }

    /**
     * @param int|string $code
     * @return $this
     */
    public function setCode(int|string $code): self
    {
        $this->code['code'] = $code;
        return $this;
    }
}
