<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Configuration;

use Dropelikeit\ResponseFactory\Enums\SerializeTypeEnum;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
interface Configuration
{
    public const string CACHE_DIR = '/serializer/';

    public const string KEY_SERIALIZE_NULL = 'serialize_null';
    public const string KEY_CACHE_DIR = 'cache_dir';
    public const string KEY_SERIALIZE_TYPE = 'serialize_type';
    public const string KEY_DEBUG = 'debug';
    public const string KEY_ADD_DEFAULT_HANDLERS = 'add_default_handlers';
    public const string KEY_CUSTOM_HANDLERS = 'custom_handlers';

    public function getCacheDir(): string;

    public function shouldSerializeNull(): bool;

    public function getSerializeType(): SerializeTypeEnum;

    public function debug(): bool;

    public function shouldAddDefaultHeaders(): bool;

    /**
     * @return array<int, CustomHandlerConfiguration|class-string>
     */
    public function getCustomHandlers(): array;
}
