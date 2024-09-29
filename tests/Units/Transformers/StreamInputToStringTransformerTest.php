<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Transformers;

use Dropelikeit\ResponseFactory\Dtos\Services\StreamInput;
use Dropelikeit\ResponseFactory\Transformers\StreamInputToStringTransformer;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

#[CoversClass(className: StreamInputToStringTransformer::class)]
#[UsesClass(className: StreamInput::class)]
final class StreamInputToStringTransformerTest extends TestCase
{
    private readonly MockObject&StreamInterface $stream;

    public function setUp(): void
    {
        parent::setUp();

        $this->stream = $this->getMockBuilder(StreamInterface::class)->getMock();
    }

    #[Test]
    public function canTransformToString(): void
    {
        $this->stream
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('foo');

        $transformed = (new StreamInputToStringTransformer())->transform(StreamInput::create($this->stream));

        $this->assertSame('foo', $transformed);
    }

    #[Test]
    public function throwInvalidArgumentExceptionBecauseContentIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Expected a different value than "".');

        $this->stream
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('');

        (new StreamInputToStringTransformer())->transform(StreamInput::create($this->stream));
    }
}
