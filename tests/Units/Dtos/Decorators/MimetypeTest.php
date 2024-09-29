<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Dtos\Decorators;

use Dropelikeit\ResponseFactory\Dtos\Decorators\Mimetype;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: Mimetype::class)]
final class MimetypeTest extends TestCase
{
    #[Test]
    public function canCreateMimetype(): void
    {
        $mimetype = Mimetype::createFromType('text/plain');

        $this->assertSame('text/plain', $mimetype->getMimetype());
    }

    #[Test]
    public function canNotCreateMimetypeBecauseGivenStringIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Expected a different value than "".');

        Mimetype::createFromType('');
    }
}
