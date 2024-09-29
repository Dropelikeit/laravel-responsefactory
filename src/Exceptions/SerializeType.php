<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Exceptions;

use Dropelikeit\ResponseFactory\Contracts\Configuration\Configuration;
use Dropelikeit\ResponseFactory\Contracts\Http\Code;
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
            sprintf(
                self::ERROR_MESSAGE,
                $type,
                Configuration::SERIALIZE_TYPE_JSON,
                Configuration::SERIALIZE_TYPE_XML
            ),
            Code::HTTP_CODE_BAD_REQUEST,
        );
    }
}
