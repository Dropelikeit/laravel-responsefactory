<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory;

use Dropelikeit\ResponseFactory\Configuration\Configuration;
use Dropelikeit\ResponseFactory\Contracts\Configuration\CustomHandlerConfiguration;
use Dropelikeit\ResponseFactory\Contracts\Decorators\MimetypeFromFileInformationDetector;
use Dropelikeit\ResponseFactory\Contracts\Factories\Transformers\InputToStringTransformerFactory as TransformerFactoryContract;
use Dropelikeit\ResponseFactory\Contracts\Http\ResponseFactory as ResponseFactoryContract;
use Dropelikeit\ResponseFactory\Contracts\Services\MimeTypeDetector as MimeTypeDetectorContract;
use Dropelikeit\ResponseFactory\Decorators\FileInfo;
use Dropelikeit\ResponseFactory\Enums\SerializeTypeEnum;
use Dropelikeit\ResponseFactory\Factories\Http\SerializerFactory;
use Dropelikeit\ResponseFactory\Factories\Transformers\InputToStringTransformerFactory;
use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use Dropelikeit\ResponseFactory\Services\MimeTypeDetector;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use Override;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class ServiceProvider extends BaseServiceProvider
{
    private const string CONFIGURATION_FILE_NAME = 'responsefactory.php';
    private const string CONFIGURATION_KEY_PACKAGE = 'responsefactory';
    private const string CONFIGURATION_DIR_PATH = __DIR__ . '/../config/' . self::CONFIGURATION_FILE_NAME;
    private const string RESPONSE_FACTORY_CONTAINER_KEY = 'ResponseFactory';
    private const string STORAGE_PATH = 'framework/cache/data';
    private const string LARAVEL_CONFIG_REPOSITORY_KEY = 'config';

    /**
     * @description Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(path: self::CONFIGURATION_DIR_PATH, key: self::CONFIGURATION_KEY_PACKAGE);

        /** @var Repository $configRepository */
        $configRepository = $this->app->get(id: self::LARAVEL_CONFIG_REPOSITORY_KEY);

        $cacheDir = $this->app->storagePath(path: self::STORAGE_PATH);
        if (!Storage::exists(path: $cacheDir)) {
            Storage::makeDirectory(path: $cacheDir);
        }

        $shouldSerializeNull = (bool) $configRepository
            ->get(key: 'responsefactory.serialize_null', default: true);
        $serializeType = $configRepository
            ->get(key: 'responsefactory.serialize_type', default: SerializeTypeEnum::JSON->value);
        Assert::stringNotEmpty($serializeType);
        $debug = (bool) $configRepository->get(key: 'laravel-jms-serializer.debug', default: false);
        $addDefaultHandlers = (bool) $configRepository->get(
            key: 'laravel-jms-serializer.add_default_handlers',
            default: true,
        );
        /** @var array<int, CustomHandlerConfiguration> $customHandlers */
        $customHandlers = (array) $configRepository->get(key: 'laravel-jms-serializer.custom_handlers', default: []);

        $config = Configuration::fromArray(config: [
            'serialize_null' => $shouldSerializeNull,
            'cache_dir' => $cacheDir,
            'serialize_type' => $serializeType,
            'debug' => $debug,
            'add_default_handlers' => $addDefaultHandlers,
            'custom_handlers' => $customHandlers,
        ]);

        $this->app->bind(abstract: TransformerFactoryContract::class, concrete: InputToStringTransformerFactory::class);
        $this->app->bind(abstract: MimetypeFromFileInformationDetector::class, concrete: FileInfo::class);

        $this->app->singleton(abstract: ResponseFactory::class, concrete: static function (Application $app) use ($config): ResponseFactory {
            $mimetypeDetector = $app->get(id: MimeTypeDetector::class);
            Assert::isInstanceOf(value: $mimetypeDetector, class: MimeTypeDetectorContract::class);

            return new ResponseFactory(
                serializer: (new SerializerFactory())->getSerializer(config: $config),
                config: $config,
                fileInformationDetector: $mimetypeDetector,
            );
        });

        $this->app->bind(abstract: ResponseFactoryContract::class, concrete: ResponseFactory::class);

        $this->app->bind(abstract: self::RESPONSE_FACTORY_CONTAINER_KEY, concrete: static fn (Application $app): ResponseFactory => $app->get(id: ResponseFactory::class));
    }

    /**
     * @description Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->publishes(
            paths: [self::CONFIGURATION_DIR_PATH => $this->getConfigPath()],
            groups: self::CONFIGURATION_KEY_PACKAGE,
        );
    }

    /**
     * @description Get the config path
     *
     * @return string
     */
    private function getConfigPath(): string
    {
        return config_path(path: self::CONFIGURATION_FILE_NAME);
    }
}
