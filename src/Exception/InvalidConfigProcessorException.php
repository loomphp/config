<?php

declare(strict_types=1);

namespace Loom\Config\Exception;

use RuntimeException;
use function sprintf;

class InvalidConfigProcessorException extends RuntimeException implements ExceptionInterface
{

    /**
     * @param string $processor
     *
     * @return InvalidConfigProcessorException
     */
    public static function fromNamedProcessor(string $processor): self
    {
        return new self(sprintf(
            'Cannot use %s as processor - class cannot be loaded.',
            $processor
        ));
    }

    /**
     * @param string $type
     *
     * @return InvalidConfigProcessorException
     */
    public static function fromUnsupportedType(string $type): self
    {
        return new self(sprintf(
            'Cannot use processor of type %s as processor - config processor must be callable.',
            $type
        ));
    }
}
