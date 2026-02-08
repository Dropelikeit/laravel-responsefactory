<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Exceptions;

use Dropelikeit\ResponseFactory\Contracts\Http\Code;
use Dropelikeit\ResponseFactory\Enums\SerializeTypeEnum;
use InvalidArgumentException;
use function sprintf;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class SerializeType extends InvalidArgumentException
{
    private const string ERROR_MESSAGE = 'Unknown given type "%s" allowed types are "%s" and "%s"';

    public static function fromUnsupportedSerializeType(string $type): self
    {
        return new self(
            message: sprintf(
                self::ERROR_MESSAGE,
                $type,
                SerializeTypeEnum::JSON->value,
                SerializeTypeEnum::XML->value,
            ),
            code: Code::HTTP_CODE_BAD_REQUEST,
        );
    }
}
