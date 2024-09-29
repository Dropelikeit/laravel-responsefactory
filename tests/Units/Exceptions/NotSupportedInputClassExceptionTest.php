<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Exceptions;

use Dropelikeit\ResponseFactory\Exceptions\NotSupportedInputClassException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(className: NotSupportedInputClassException::class)]
final class NotSupportedInputClassExceptionTest extends TestCase
{
    #[Test]
    public function canCreateInstance(): void
    {
        $exception = new NotSupportedInputClassException('error message', 0, null);

        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    #[Test]
    public function canCreateFromConfig(): void
    {
        $exception = NotSupportedInputClassException::create('my_class');

        $this->assertInstanceOf(RuntimeException::class, $exception);
    }
}
