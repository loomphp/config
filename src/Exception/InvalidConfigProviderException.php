<?php

declare(strict_types=1);

namespace Loom\Config\Exception;

use RuntimeException;
use function sprintf;

class InvalidConfigProviderException extends RuntimeException implements ExceptionInterface
{

    /**
     * @param string $provider
     *
     * @return InvalidConfigProviderException
     */
    public static function fromNamedProvider(string $provider): self
    {
        return new self(sprintf(
            'Cannot read config from %s - class cannot be loaded.',
            $provider
        ));
    }

    /**
     * @param string $type
     *
     * @return InvalidConfigProviderException
     */
    public static function fromUnsupportedType(string $type): self
    {
        return new self(
            sprintf("Cannot read config from %s - config provider must be callable.", $type)
        );
    }
}
