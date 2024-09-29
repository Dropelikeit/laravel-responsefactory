<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Exceptions;

use Dropelikeit\ResponseFactory\Contracts\Http\Code;
use RuntimeException;

use function sprintf;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class NotSupportedInputClassException extends RuntimeException
{
    private const string ERROR_MESSAGE = 'Given class "%s" is not supported yet.';

    public static function create(string $class): self
    {
        return new self(
            sprintf(self::ERROR_MESSAGE, $class),
            Code::HTTP_CODE_INTERNAL_SERVER_ERROR,
        );
    }
}
