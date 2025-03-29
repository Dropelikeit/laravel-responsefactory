<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Dtos\Services;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Override;
use Psr\Http\Message\StreamInterface;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 *
 * @template-implements Input<StreamInterface>
 */
final readonly class StreamInput implements Input
{
    private function __construct(private StreamInterface $value)
    {
    }

    public static function create(StreamInterface $stream): self
    {
        return new self(value: $stream);
    }

    #[Override]
    public function getValue(): StreamInterface
    {
        return $this->value;
    }
}
