<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Http;

use ArrayIterator;
use Dropelikeit\ResponseFactory\Contracts\Configuration\Configuration;
use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Dropelikeit\ResponseFactory\Contracts\Http\Code;
use Dropelikeit\ResponseFactory\Contracts\Http\Header;
use Dropelikeit\ResponseFactory\Contracts\Http\ResponseFactory as ResponseFactoryContract;
use Dropelikeit\ResponseFactory\Contracts\Services\MimeTypeDetector as MimeTypeDetectorContract;
use Dropelikeit\ResponseFactory\Exceptions\SerializeType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as LaravelResponse;
use function in_array;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Override;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class ResponseFactory implements ResponseFactoryContract
{
    private const string CONTENT_DISPOSITION_HEADER_FORMAT = '%s; %s="%s";';

    /**
     * @psalm-var Configuration::SERIALIZE_TYPE_*
     */
    private string $serializeType;

    /**
     * @psalm-var Code::HTTP_CODE_*
     */
    private int $status;
    private ?SerializationContext $context;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Configuration $config,
        private readonly MimeTypeDetectorContract $fileInformationDetector,
    ) {
        $this->serializeType = $config->getSerializeType();
        $this->status = Code::HTTP_CODE_OK;
        $this->context = null;
    }

    /**
     * @psalm-param Code::HTTP_CODE_* $code
     */
    #[Override]
    public function withStatusCode(int $code): void
    {
        $this->status = $code;
    }

    #[Override]
    public function withContext(SerializationContext $context): void
    {
        $this->context = $context;
    }

    #[Override]
    public function withSerializeType(string $serializeType): ResponseFactoryContract
    {
        if (!in_array($serializeType, [
            Configuration::SERIALIZE_TYPE_JSON,
            Configuration::SERIALIZE_TYPE_XML
        ], true)) {
            /** @psalm-suppress NoValue */
            throw SerializeType::fromUnsupportedSerializeType($serializeType);
        }

        $instance = new self($this->serializer, $this->config, $this->fileInformationDetector);
        $instance->serializeType = $serializeType;

        return $instance;
    }

    #[Override]
    public function create(object $jmsResponse): Response
    {
        $initialType = $this->getInitialType($jmsResponse);

        $content = $this->serializer->serialize(
            $jmsResponse,
            $this->serializeType,
            $this->context,
            $initialType
        );
        Assert::stringNotEmpty($content);

        return $this->getResponse($content);
    }

    #[Override]
    public function createFromArray(array $jmsResponse): Response
    {
        $content = $this->serializer->serialize($jmsResponse, $this->serializeType, $this->context);
        Assert::stringNotEmpty($content);

        return $this->getResponse($content);
    }

    #[Override]
    public function createSilent(): Response
    {
        return new LaravelResponse(status: Code::HTTP_CODE_NO_CONTENT);
    }

    /**
     * @psalm-param non-empty-string $filename
     */
    #[Override]
    public function createByFile(Input $input, string $filename): Response
    {
        $mimetype = $this->fileInformationDetector->detect($input);

        return new Response($input->getValue(), Code::HTTP_CODE_OK, headers: [
            Header::HEADER_CONTENT_TYPE => $mimetype,
            Header::HEADER_CONTENT_ENCODING => Header::HEADER_CONTENT_ENCODING_BINARY,
            Header::HEADER_CONTENT_DISPOSITION => sprintf(
                self::CONTENT_DISPOSITION_HEADER_FORMAT,
                Header::HEADER_CONTENT_DISPOSITION_ATTACHMENT,
                Header::HEADER_CONTENT_DISPOSITION_FILENAME,
                $filename,
            ),
        ]);
    }

    private function getInitialType(object $jmsResponse): ?string
    {
        if ($jmsResponse instanceof ArrayIterator) {
            return self::SERIALIZER_INITIAL_TYPE_ARRAY;
        }

        return null;
    }

    /**
     * @psalm-param non-empty-string $content
     */
    private function getResponse(string $content): Response
    {
        if ($this->serializeType === Configuration::SERIALIZE_TYPE_XML) {
            return new LaravelResponse(
                content: $content,
                status: $this->status,
                headers: [Header::HEADER_CONTENT_TYPE => Header::HEADER_CONTENT_XML]
            );
        }

        return new JsonResponse(
            data: $content,
            status: $this->status,
            headers: [Header::HEADER_ACCEPT => Header::HEADER_CONTENT_JSON],
            json: true
        );
    }
}
