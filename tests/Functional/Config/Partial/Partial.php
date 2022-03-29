<?php

namespace Hippy\Tests\Functional\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Partial extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'test';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);

        $this->def = [
            'test.value' => '__dummy_value__',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('test.value', 'string', $config);

        return $this;
    }
}
