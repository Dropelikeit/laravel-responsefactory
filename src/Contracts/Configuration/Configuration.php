<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Configuration;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
interface Configuration
{
    public const string SERIALIZE_TYPE_JSON = 'json';
    public const string SERIALIZE_TYPE_XML = 'xml';
    public const string CACHE_DIR = '/serializer/';

    public const string KEY_SERIALIZE_NULL = 'serialize_null';
    public const string KEY_CACHE_DIR = 'cache_dir';
    public const string KEY_SERIALIZE_TYPE = 'serialize_type';
    public const string KEY_DEBUG = 'debug';
    public const string KEY_ADD_DEFAULT_HANDLERS = 'add_default_handlers';
    public const string KEY_CUSTOM_HANDLERS = 'custom_handlers';

    public function getCacheDir(): string;

    public function shouldSerializeNull(): bool;

    /**
     * @psalm-return self::SERIALIZE_TYPE_*
     */
    public function getSerializeType(): string;

    public function debug(): bool;

    public function shouldAddDefaultHeaders(): bool;

    /**
     * @return array<int, CustomHandlerConfiguration|class-string>
     */
    public function getCustomHandlers(): array;
}
