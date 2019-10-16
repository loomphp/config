<?php

declare(strict_types=1);

namespace LoomTest\Config\Resources;

use ArrayObject;

class FooConfigProvider
{
    /**
     * @return array|ArrayObject
     */
    public function __invoke()
    {
        return ['foo' => 'bar'];
    }
}
