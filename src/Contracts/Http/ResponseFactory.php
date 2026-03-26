<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Http;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Dropelikeit\ResponseFactory\Enums\SerializeTypeEnum;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;

interface ResponseFactory
{
    public const string SERIALIZER_INITIAL_TYPE_ARRAY = 'array';

    /**
     * @psalm-param Code::HTTP_CODE_* $code
     */
    public function withStatusCode(int $code): void;

    public function withContext(SerializationContext $context): void;

    public function withSerializeType(SerializeTypeEnum $serializeType): self;

    public function create(object $jmsResponse): Response;

    /**
     * @param array<int|string, object> $jmsResponse
     */
    public function createFromArray(array $jmsResponse): Response;

    /**
     * @description Create a response without a body
     */
    public function createSilent(): Response;

    /**
     * @psalm-param non-empty-string $filename
     */
    public function createByFile(Input $input, string $filename): Response;
}
