<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Exceptions;

use Dropelikeit\ResponseFactory\Exceptions\SerializeType;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: SerializeType::class)]
final class SerializeTypeTest extends TestCase
{
    #[Test]
    public function canCreateInstance(): void
    {
        $exception = new SerializeType('error message', 0, null);

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
    }

    #[Test]
    public function canCreateFromConfig(): void
    {
        $exception = SerializeType::fromUnsupportedSerializeType('tsv');

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
    }
}
