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
        $this->mergeConfigFrom(self::CONFIGURATION_DIR_PATH, self::CONFIGURATION_KEY_PACKAGE);

        /** @var Repository $configRepository */
        $configRepository = $this->app->get(self::LARAVEL_CONFIG_REPOSITORY_KEY);

        $cacheDir = $this->app->storagePath(self::STORAGE_PATH);
        if (!Storage::exists($cacheDir)) {
            Storage::makeDirectory($cacheDir);
        }

        $shouldSerializeNull = (bool) $configRepository
            ->get('responsefactory.serialize_null', true);
        $serializeType = $configRepository
            ->get('responsefactory.serialize_type', Configuration::SERIALIZE_TYPE_JSON);
        Assert::stringNotEmpty($serializeType);
        $debug = (bool) $configRepository->get('laravel-jms-serializer.debug', false);
        $addDefaultHandlers = (bool) $configRepository->get(
            'laravel-jms-serializer.add_default_handlers',
            true
        );
        /** @var array<int, CustomHandlerConfiguration> $customHandlers */
        $customHandlers = (array) $configRepository->get('laravel-jms-serializer.custom_handlers', []);

        $config = Configuration::fromArray([
            'serialize_null' => $shouldSerializeNull,
            'cache_dir' => $cacheDir,
            'serialize_type' => $serializeType,
            'debug' => $debug,
            'add_default_handlers' => $addDefaultHandlers,
            'custom_handlers' => $customHandlers,
        ]);

        $this->app->bind(TransformerFactoryContract::class, InputToStringTransformerFactory::class);
        $this->app->bind(MimetypeFromFileInformationDetector::class, FileInfo::class);

        $this->app->singleton(ResponseFactory::class, static function (Application $app) use ($config): ResponseFactory {
            $mimetypeDetector = $app->get(MimeTypeDetector::class);
            Assert::isInstanceOf($mimetypeDetector, MimeTypeDetectorContract::class);

            return new ResponseFactory(
                (new SerializerFactory())->getSerializer($config),
                $config,
                $mimetypeDetector,
            );
        });

        $this->app->bind(ResponseFactoryContract::class, ResponseFactory::class);

        $this->app->bind(self::RESPONSE_FACTORY_CONTAINER_KEY, static fn (Application $app): ResponseFactory => $app->get(ResponseFactory::class));
    }

    /**
     * @description Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->publishes(
            [self::CONFIGURATION_DIR_PATH => $this->getConfigPath()],
            self::CONFIGURATION_KEY_PACKAGE,
        );
    }

    /**
     * @description Get the config path
     *
     * @return string
     */
    private function getConfigPath(): string
    {
        return config_path(self::CONFIGURATION_FILE_NAME);
    }
}
