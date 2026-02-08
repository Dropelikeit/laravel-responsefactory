<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Factories\Http;

use function assert;
use function class_exists;
use Dropelikeit\ResponseFactory\Contracts\Configuration\Configuration;
use Dropelikeit\ResponseFactory\Contracts\Configuration\CustomHandlerConfiguration;
use Dropelikeit\ResponseFactory\Contracts\Factories\Http\SerializerFactory as SerializerFactoryContract;
use function is_string;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

use JMS\Serializer\SerializerInterface;
use Override;
use function sprintf;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final readonly class SerializerFactory implements SerializerFactoryContract
{
    #[Override]
    public function getSerializer(Configuration $config): SerializerInterface
    {
        $builder = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                propertyNamingStrategy: new SerializedNameAnnotationStrategy(
                    namingStrategy: new IdenticalPropertyNamingStrategy()
                )
            )
            ->addDefaultListeners()
            ->setSerializationContextFactory(serializationContextFactory: static function () use ($config): SerializationContext {
                // @codeCoverageIgnoreStart
                return SerializationContext::create()->setSerializeNull(bool: $config->shouldSerializeNull());
                // @codeCoverageIgnoreEnd
            });

        if ($config->shouldAddDefaultHeaders()) {
            $builder->addDefaultHandlers();
        }

        $customHandlers = $config->getCustomHandlers();
        if ($customHandlers !== []) {
            $builder->configureHandlers(closure: function (HandlerRegistry $registry) use ($customHandlers): void {
                foreach ($customHandlers as $customHandler) {
                    if (is_string($customHandler) && class_exists($customHandler)) {
                        $customHandler = new $customHandler();
                    }

                    Assert::implementsInterface(
                        value: $customHandler,
                        interface: CustomHandlerConfiguration::class,
                        message: sprintf(
                            'Its required to implement the "%s" interface',
                            CustomHandlerConfiguration::class
                        )
                    );
                    /** @phpstan-ignore-next-line */
                    assert(assertion: $customHandler instanceof CustomHandlerConfiguration);

                    $registry->registerHandler(
                        direction: $customHandler->getDirection(),
                        typeName: $customHandler->getTypeName(),
                        format: $customHandler->getFormat(),
                        handler: $customHandler->getCallable(),
                    );
                }
            });
        }

        $cacheDir = $config->getCacheDir();
        if ($cacheDir !== '') {
            $builder->setCacheDir(dir: $cacheDir);
        }

        return $builder->setDebug(bool: $config->debug())->build();
    }
}
