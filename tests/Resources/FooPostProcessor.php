<?php

declare(strict_types=1);

namespace LoomTest\Config\Resources;

class FooPostProcessor
{

    /**
     * @param array $config
     *
     * @return array
     */
    public function __invoke(array $config)
    {
        return $config + ['post-processed' => true];
    }
}
