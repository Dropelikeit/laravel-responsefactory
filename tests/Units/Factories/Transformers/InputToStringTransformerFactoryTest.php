<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Factories\Transformers;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Dropelikeit\ResponseFactory\Dtos\Services\StreamInput;
use Dropelikeit\ResponseFactory\Dtos\Services\StringInput;
use Dropelikeit\ResponseFactory\Exceptions\NotSupportedInputClassException;
use Dropelikeit\ResponseFactory\Factories\Transformers\InputToStringTransformerFactory;
use Dropelikeit\ResponseFactory\Tests\data\Units\Factories\Transformers\InputToStringTransformerFactory\UnkownInput;
use Dropelikeit\ResponseFactory\Transformers\StreamInputToStringTransformer;
use Dropelikeit\ResponseFactory\Transformers\StringInputToStringTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

#[CoversClass(InputToStringTransformerFactory::class)]
#[UsesClass(className: StringInputToStringTransformer::class)]
#[UsesClass(className: StreamInputToStringTransformer::class)]
#[UsesClass(className: StreamInput::class)]
#[UsesClass(className: StringInput::class)]
#[UsesClass(className: NotSupportedInputClassException::class)]
final class InputToStringTransformerFactoryTest extends TestCase
{
    private readonly MockObject&StreamInterface $stream;

    public function setUp(): void
    {
        parent::setUp();

        $this->stream = $this->getMockBuilder(StreamInterface::class)->getMock();
    }

    #[Test]
    #[TestDox('can create transformer successfully with input of type $expectedClassAsInstance')]
    #[DataProvider(methodName: 'dataProviderCanCreateTransformerSuccessful')]
    public function canCreateTransformerSuccessful(Input $input, string $expectedClassAsInstance): void
    {
        $transformer = (new InputToStringTransformerFactory())->factorize($input);

        $this->assertInstanceOf($expectedClassAsInstance, $transformer);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function dataProviderCanCreateTransformerSuccessful(): array
    {
        $stream = new class() implements StreamInterface {

            public function __toString(): string
            {
                return 'hello world';
            }

            public function close(): void
            {
            }

            public function detach()
            {
            }

            public function getSize(): ?int
            {
                return null;
            }

            public function tell(): int
            {
                return 0;
            }

            public function eof(): bool
            {
                return true;
            }

            public function isSeekable(): bool
            {
                return false;
            }

            public function seek(int $offset, int $whence = SEEK_SET): void
            {
            }

            public function rewind(): void
            {
            }

            public function isWritable(): bool
            {
                return false;
            }

            public function write(string $string): int
            {
                return 0;
            }

            public function isReadable(): bool
            {
                return true;
            }

            public function read(int $length): string
            {
                return 'hello world';
            }

            public function getContents(): string
            {
                return 'hello world';
            }

            public function getMetadata(?string $key = null)
            {
            }
        };

        return [
            'string_to_string_transformer' => [
                StringInput::create('hello world'),
                StringInputToStringTransformer::class,
            ],
            'stream_to_string_transformer' => [
                StreamInput::create($stream),
                StreamInputToStringTransformer::class,
            ],
        ];
    }

    #[Test]
    public function throwAUnsupportedInputTypeWhenTheInputIsNotMatched(): void
    {
        $this->expectException(NotSupportedInputClassException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Given class "Dropelikeit\ResponseFactory\Tests\data\Units\Factories\Transformers\InputToStringTransformerFactory\UnkownInput" is not supported yet.');

        (new InputToStringTransformerFactory())->factorize(new UnkownInput());
    }
}
