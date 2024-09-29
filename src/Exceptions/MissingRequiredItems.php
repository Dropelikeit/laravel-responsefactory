<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Exceptions;

use Dropelikeit\ResponseFactory\Contracts\Http\Code;
use InvalidArgumentException;
use function sprintf;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class MissingRequiredItems extends InvalidArgumentException
{
    private const string ERROR_MESSAGE = 'Missing required fields, please check your serializer-config. Missing fields "%s"';

    public static function fromConfig(string $fields): self
    {
        return new self(sprintf(self::ERROR_MESSAGE, $fields), Code::HTTP_CODE_BAD_REQUEST);
    }
}
