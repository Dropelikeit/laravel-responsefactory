<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Transformers;

use Dropelikeit\ResponseFactory\Dtos\Services\StringInput;
use Dropelikeit\ResponseFactory\Transformers\StringInputToStringTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: StringInputToStringTransformer::class)]
#[UsesClass(className: StringInput::class)]
final class StringInputToStringTransformerTest extends TestCase
{
    #[Test]
    public function canTransformToString(): void
    {
        $transformed = (new StringInputToStringTransformer())->transform(StringInput::create('content'));

        $this->assertSame('content', $transformed);
    }
}
