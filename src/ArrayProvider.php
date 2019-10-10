<?php

declare(strict_types=1);

namespace Loom\Config;

/**
 * Provider that returns the array seeded to itself.
 *
 * Primary use case is configuration cache-related settings.
 */
class ArrayProvider
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function __invoke(): array
    {
        return $this->config;
    }
}
