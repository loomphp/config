<?php

declare(strict_types=1);

namespace LoomTest\Config;

use Loom\Config\ArrayProvider;
use PHPUnit\Framework\TestCase;

class ArrayProviderTest extends TestCase
{
    public function testProviderIsCallable()
    {
        $provider = new ArrayProvider([]);
        $this->assertInternalType('callable', $provider);
    }

    public function testProviderReturnsArrayProvidedAtConstruction()
    {
        $expected = [
            'foo' => 'bar',
        ];
        $provider = new ArrayProvider($expected);

        $this->assertSame($expected, $provider());
    }
}
