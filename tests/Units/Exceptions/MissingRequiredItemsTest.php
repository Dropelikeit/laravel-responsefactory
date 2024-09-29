<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Exceptions;

use Dropelikeit\ResponseFactory\Exceptions\MissingRequiredItems;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: MissingRequiredItems::class)]
final class MissingRequiredItemsTest extends TestCase
{
    #[Test]
    public function canCreateInstance(): void
    {
        $exception = new MissingRequiredItems('error message', 0, null);

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
    }

    #[Test]
    public function canCreateFromConfig(): void
    {
        $exception = MissingRequiredItems::fromConfig('my_field');

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
    }
}
