<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Services;

use Dropelikeit\ResponseFactory\Contracts\Decorators\MimetypeFromFileInformationDetector;
use Dropelikeit\ResponseFactory\Contracts\Factories\Transformers\InputToStringTransformerFactory;
use Dropelikeit\ResponseFactory\Contracts\Transformers\InputTransformer;
use Dropelikeit\ResponseFactory\Dtos\Decorators\Mimetype;
use Dropelikeit\ResponseFactory\Dtos\Services\StreamInput;
use Dropelikeit\ResponseFactory\Dtos\Services\StringInput;
use Dropelikeit\ResponseFactory\Services\MimeTypeDetector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

#[CoversClass(className: MimeTypeDetector::class)]
#[UsesClass(className: Mimetype::class)]
#[UsesClass(className: StreamInput::class)]
#[UsesClass(className: StringInput::class)]
final class MimeTypeDectorTest extends TestCase
{
    private readonly MockObject&InputToStringTransformerFactory $inputToStringTransformerFactory;
    private readonly MockObject&InputTransformer $transformer;
    private readonly MockObject&MimetypeFromFileInformationDetector $mimeTypeFromFileInformationDetector;

    public function setUp(): void
    {
        parent::setUp();

        $this->inputToStringTransformerFactory = $this
            ->getMockBuilder(InputToStringTransformerFactory::class)
            ->getMock();

        $this->transformer = $this->getMockBuilder(InputTransformer::class)->getMock();

        $this->mimeTypeFromFileInformationDetector = $this
            ->getMockBuilder(MimeTypeFromFileInformationDetector::class)
            ->getMock();
    }

    #[Test]
    public function canDetectMimeTypeFromGivenStream(): void
    {
        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();
        $streamInput = StreamInput::create($stream);

        $this->transformer
            ->expects(self::once())
            ->method('transform')
            ->with($streamInput)
            ->willReturn('{"foo": "bar"}');

        $this->inputToStringTransformerFactory
            ->expects(self::once())
            ->method('factorize')
            ->with($streamInput)
            ->willReturn($this->transformer);

        $this->mimeTypeFromFileInformationDetector
            ->expects(self::once())
            ->method('fetchMimetypeByGivenString')
            ->with('{"foo": "bar"}')
            ->willReturn(Mimetype::createFromType('application/json'));

        $detector = new MimeTypeDetector(
            $this->inputToStringTransformerFactory,
            $this->mimeTypeFromFileInformationDetector,
        );

        $mimeType = $detector->detect($streamInput);

        $this->assertSame('application/json', $mimeType);
    }

    #[Test]
    public function canDetectMimeTypeFromGivenString(): void
    {
        $stringInput = StringInput::create('{"foo": "bar"}');

        $this->transformer
            ->expects(self::once())
            ->method('transform')
            ->with($stringInput)
            ->willReturn('{"foo": "bar"}');

        $this->inputToStringTransformerFactory
            ->expects(self::once())
            ->method('factorize')
            ->with($stringInput)
            ->willReturn($this->transformer);

        $this->mimeTypeFromFileInformationDetector
            ->expects(self::once())
            ->method('fetchMimetypeByGivenString')
            ->with('{"foo": "bar"}')
            ->willReturn(Mimetype::createFromType('application/json'));

        $detector = new MimeTypeDetector(
            $this->inputToStringTransformerFactory,
            $this->mimeTypeFromFileInformationDetector,
        );

        $mimeType = $detector->detect($stringInput);

        $this->assertSame('application/json', $mimeType);
    }
}
