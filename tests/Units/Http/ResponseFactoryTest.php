<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Http;

use Dropelikeit\ResponseFactory\Contracts\Configuration\Configuration;
use Dropelikeit\ResponseFactory\Contracts\Services\MimeTypeDetector;
use Dropelikeit\ResponseFactory\Dtos\Services\StringInput;
use Dropelikeit\ResponseFactory\Enums\SerializeTypeEnum;
use Dropelikeit\ResponseFactory\Exceptions\SerializeType;
use Dropelikeit\ResponseFactory\Factories\Http\SerializerFactory;
use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory\Dummy;
use Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory\JsonDummy;
use Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory\Response;
use Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory\XmlDummy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as LaravelResponse;
use InvalidArgumentException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: ResponseFactory::class)]
#[UsesClass(className: SerializeType::class)]
#[UsesClass(className: SerializerFactory::class)]
#[UsesClass(className: StringInput::class)]
final class ResponseFactoryTest extends TestCase
{
    private readonly MockObject&Configuration $config;
    private readonly MockObject&SerializerInterface $serializer;
    private readonly MockObject&MimeTypeDetector $mimeTypeDetector;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        $this->config = $this
            ->getMockBuilder(className: Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializer = $this
            ->getMockBuilder(className: SerializerInterface::class)
            ->getMock();

        $this->mimeTypeDetector = $this->getMockBuilder(className: MimeTypeDetector::class)->getMock();
    }

    #[Test]
    public function canCreateResponse(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            (new SerializerFactory())->getSerializer($this->config),
            $this->config,
            $this->mimeTypeDetector,
        );

        $response = $responseFactory->create(new Dummy());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"amount":12,"text":"Hello World!"}', $response->getContent());
    }

    #[Test]
    public function canCreateFromArrayIterator(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            (new SerializerFactory())->getSerializer($this->config),
            $this->config,
            $this->mimeTypeDetector,
        );

        $response = $responseFactory->create(Response::create([new Response\Item()]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('[{"key":"magic_number","value":12}]', $response->getContent());
    }

    #[Test]
    public function canCreateJsonResponseFromArray(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            (new SerializerFactory())->getSerializer($this->config),
            $this->config,
            $this->mimeTypeDetector,
        );

        $response = $responseFactory->createFromArray(require __DIR__ . '/../../data/Units/Http/ResponseFactory/dummy_array.php');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            '{"some_objects":{"person":{"first_name":"Max","last_name":"Mustermann","birthdate":"01.01.1976","birth_place":"Berlin","nationality":"german"}}}',
            $response->getContent()
        );
    }

    #[Test]
    public function canCreateXmlResponseFromArray(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::XML);

        $responseFactory = new ResponseFactory(
            (new SerializerFactory())->getSerializer($this->config),
            $this->config,
            $this->mimeTypeDetector,
        );

        $response = $responseFactory->createFromArray(require __DIR__ . '/../../data/Units/Http/ResponseFactory/dummy_array.php');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry>
    <entry>
      <entry><![CDATA[Max]]></entry>
      <entry><![CDATA[Mustermann]]></entry>
      <entry><![CDATA[01.01.1976]]></entry>
      <entry><![CDATA[Berlin]]></entry>
      <entry><![CDATA[german]]></entry>
    </entry>
  </entry>
</result>
',
            $response->getContent()
        );
    }

    #[Test]
    public function canChangeStatusCode(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            (new SerializerFactory())->getSerializer($this->config),
            $this->config,
            $this->mimeTypeDetector,
        );

        $responseFactory->withStatusCode(404);

        $response = $responseFactory->create(new Dummy());

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('{"amount":12,"text":"Hello World!"}', $response->getContent());
    }

    #[Test]
    public function canUseGivenContext(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            (new SerializerFactory())->getSerializer($this->config),
            $this->config,
            $this->mimeTypeDetector,
        );
        $responseFactory->withContext(SerializationContext::create()->setSerializeNull(true));

        $response = $responseFactory->create(new Dummy());

        self::assertEquals('{"amount":12,"text":"Hello World!","items":null}', $response->getContent());
    }

    #[Test]
    #[DataProvider(methodName: 'dataProviderCanSerializeWithSerializeType')]
    public function canSerializeWithSerializeType(SerializeTypeEnum $changeSerializeTypeTo, string $expectedResult): void
    {
        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->exactly(count: 2))
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            (new SerializerFactory())->getSerializer($this->config),
            $this->config,
            $this->mimeTypeDetector,
        );
        $responseFactory->withContext(SerializationContext::create()->setSerializeNull(bool: true));
        $responseFactory = $responseFactory->withSerializeType(serializeType: $changeSerializeTypeTo);

        $response = $responseFactory->create(new Dummy());

        self::assertEquals(
            $expectedResult,
            $response->getContent()
        );
    }

    /**
     * @return array<string, array<int, string>>
     * @psalm-return array{with_json: array<int, string>, 'with_xml': array<int, string>}
     */
    public static function dataProviderCanSerializeWithSerializeType(): array
    {
        return [
            'with_json' => [
                SerializeTypeEnum::JSON,
                '{"amount":12,"text":"Hello World!"}',
            ],
            'with_xml' => [
                SerializeTypeEnum::XML,
                '<?xml version="1.0" encoding="UTF-8"?>
<result>
  <amount>12</amount>
  <text><![CDATA[Hello World!]]></text>
</result>
',
            ],
        ];
    }

    #[Test]
    public function canCreateSilentResponse(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            serializer: (new SerializerFactory())->getSerializer(config: $this->config),
            config: $this->config,
            fileInformationDetector: $this->mimeTypeDetector,
        );

        $response = $responseFactory->createSilent();

        $this->assertEquals(expected: new LaravelResponse(status: 204), actual: $response);
    }

    #[Test]
    public function throwInvalidArgumentExceptionIfContentOnCreateMethodIsEmpty(): void
    {
        $this->expectException(exception: InvalidArgumentException::class);
        $this->expectExceptionCode(code: 0);
        $this->expectExceptionMessage(message: 'Expected a different value than "".');

        $object = new Dummy();

        $this->config
            ->expects($this->never())
            ->method('getCacheDir');

        $this->config
            ->expects($this->never())
            ->method('debug');

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($object, 'json', null, null)
            ->wilLReturn('');

        (new ResponseFactory(
            serializer: $this->serializer,
            config: $this->config,
            fileInformationDetector: $this->mimeTypeDetector
        ))->create(jmsResponse: $object);
    }

    #[Test]
    public function throwInvalidArgumentExceptionIfContentOnCreateFromArrayMethodIsEmpty(): void
    {
        $this->expectException(exception: InvalidArgumentException::class);
        $this->expectExceptionCode(code: 0);
        $this->expectExceptionMessage(message: 'Expected a different value than "".');

        $object = new Dummy();

        $this->config
            ->expects($this->never())
            ->method('getCacheDir');

        $this->config
            ->expects($this->never())
            ->method('debug');

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with([$object], 'json', null)
            ->wilLReturn('');

        (new ResponseFactory(
            serializer: $this->serializer,
            config: $this->config,
            fileInformationDetector: $this->mimeTypeDetector
        ))->createFromArray(jmsResponse: [$object]);
    }

    #[Test]
    public function canDetectIfSerializeTypeIsXmlResultResponseHasXml(): void
    {
        $expectedResponse = new LaravelResponse(
            content: '<?xml version="1.0" encoding="UTF-8"?>
<XmlDummy title="My test"/>
',
            status: 200,
            headers: ['Content-Type' => 'application/xml']
        );

        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::XML);

        $responseFactory = new ResponseFactory(
            serializer: (new SerializerFactory())->getSerializer($this->config),
            config: $this->config,
            fileInformationDetector: $this->mimeTypeDetector,
        );

        $response = $responseFactory->create(jmsResponse: new XmlDummy());

        $this->assertEquals(expected: $expectedResponse, actual: $response);
    }

    #[Test]
    public function canDetectIfSerializeTypeIsJSONResultResponseHasJSON(): void
    {
        $expectedResponse = new JsonResponse(
            data: '{"title":"My test"}',
            status: 200,
            headers: ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            json: true
        );

        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            (new SerializerFactory())->getSerializer($this->config),
            $this->config,
            $this->mimeTypeDetector,
        );

        $response = $responseFactory->create(jmsResponse: new JsonDummy());

        $this->assertEquals(expected: $expectedResponse, actual: $response);
    }

    #[Test]
    public function canGetFileResponseAsNonDownload(): void
    {
        $expectedResponse = new \Symfony\Component\HttpFoundation\Response(
            content: '{"foo": "bar"}',
            status: 200,
            headers: [
                'Content-Type' => 'application/json',
                'Content-Encoding' => 'binary',
                'Content-Disposition' => 'attachment; filename="my-test.json";'
            ],
        );

        $stringInput = StringInput::create(content: '{"foo": "bar"}');

        $this->mimeTypeDetector
            ->expects($this->once())
            ->method('detect')
            ->with($stringInput)
            ->willReturn('application/json');

        $this->config
            ->expects($this->once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects($this->once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects($this->once())
            ->method('getSerializeType')
            ->willReturn(SerializeTypeEnum::JSON);

        $responseFactory = new ResponseFactory(
            serializer: (new SerializerFactory())->getSerializer($this->config),
            config: $this->config,
            fileInformationDetector: $this->mimeTypeDetector,
        );

        $response = $responseFactory->createByFile(input: StringInput::create(content: '{"foo": "bar"}'), filename: 'my-test.json');

        $this->assertEquals(expected: $expectedResponse, actual: $response);
    }
}
