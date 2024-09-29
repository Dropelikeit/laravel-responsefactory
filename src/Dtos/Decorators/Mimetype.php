<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Dtos\Decorators;

use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final readonly class Mimetype
{
    /**
     * @psalm-param non-empty-string $mimetype
     */
    private function __construct(private string $mimetype)
    {

    }

    public static function createFromType(string $mimetype): self
    {
        Assert::stringNotEmpty($mimetype);

        return new self(mimetype: $mimetype);
    }

    /**
     * @psalm-return non-empty-string
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }
}
