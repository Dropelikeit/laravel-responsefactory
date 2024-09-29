<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Dtos\Services;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 *
 * @template-implements Input<non-empty-string>
 */
final readonly class StringInput implements Input
{
    /**
     * @psalm-param non-empty-string $value
     */
    private function __construct(private string $value)
    {
    }

    public static function create(string $content): self
    {
        Assert::stringNotEmpty($content);

        return new self(value: $content);
    }

    /**
     * @psalm-return non-empty-string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
