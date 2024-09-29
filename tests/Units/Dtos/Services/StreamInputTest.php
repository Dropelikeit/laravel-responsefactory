<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Dtos\Services;

use Dropelikeit\ResponseFactory\Dtos\Services\StreamInput;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

#[CoversClass(className: StreamInput::class)]
final class StreamInputTest extends TestCase
{
    private readonly MockObject&StreamInterface $stream;

    public function setUp(): void
    {
        parent::setUp();

        $this->stream = $this->getMockBuilder(StreamInterface::class)->getMock();
    }

    #[Test]
    public function canCreateStreamInput(): void
    {
        $input = StreamInput::create($this->stream);

        $this->assertSame($this->stream, $input->getValue());
    }
}
