<?php

namespace Hippy\Tests\Functional\Config;

use Hippy\Config\Config;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** @coversDefaultClass \Hippy\Config\Config */
class ConfigTest extends WebTestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::get
     */
    public function testPartialInjection(): void
    {
        $value = '__dummy_value__';

        $container = static::getContainer();
        $config = $container->get(Config::class);
        if (!($config instanceof Config)) {
            $this->fail('unable to retrieve config object');
        }

        $this->assertEquals($value, $config->get('test.value'));
    }
}
