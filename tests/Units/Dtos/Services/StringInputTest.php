<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Dtos\Services;

use Dropelikeit\ResponseFactory\Dtos\Services\StringInput;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: StringInput::class)]
final class StringInputTest extends TestCase
{
    #[Test]
    public function canCreateStringInput(): void
    {
        $input = StringInput::create('test');

        $this->assertSame('test', $input->getValue());
    }

    #[Test]
    public function canNotCreateStringInputWithAnEmptyValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Expected a different value than "".');

        StringInput::create('');
    }
}
