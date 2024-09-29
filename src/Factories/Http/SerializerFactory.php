<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Factories\Http;

use Dropelikeit\ResponseFactory\Contracts\Configuration\Configuration;
use Dropelikeit\ResponseFactory\Contracts\Configuration\CustomHandlerConfiguration;
use Dropelikeit\ResponseFactory\Contracts\Factories\Http\SerializerFactory as SerializerFactoryContract;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final readonly class SerializerFactory implements SerializerFactoryContract
{
    public function getSerializer(Configuration $config): SerializerInterface
    {
        $builder = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->addDefaultListeners()
            ->setSerializationContextFactory(static function () use ($config): SerializationContext {
                // @codeCoverageIgnoreStart
                return SerializationContext::create()->setSerializeNull($config->shouldSerializeNull());
                // @codeCoverageIgnoreEnd
            });

        if ($config->shouldAddDefaultHeaders()) {
            $builder->addDefaultHandlers();
        }

        $customHandlers = $config->getCustomHandlers();
        if ($customHandlers !== []) {
            $builder->configureHandlers(function (HandlerRegistry $registry) use ($customHandlers): void {
                foreach ($customHandlers as $customHandler) {
                    if (is_string($customHandler) && class_exists($customHandler)) {
                        $customHandler = new $customHandler();
                    }

                    Assert::implementsInterface(
                        $customHandler,
                        CustomHandlerConfiguration::class,
                        sprintf(
                            'Its required to implement the "%s" interface',
                            CustomHandlerConfiguration::class
                        )
                    );
                    /** @phpstan-ignore-next-line */
                    assert($customHandler instanceof CustomHandlerConfiguration);

                    $registry->registerHandler(
                        $customHandler->getDirection(),
                        $customHandler->getTypeName(),
                        $customHandler->getFormat(),
                        $customHandler->getCallable(),
                    );
                }
            });
        }

        $cacheDir = $config->getCacheDir();
        if ($cacheDir !== '') {
            $builder->setCacheDir($cacheDir);
        }

        return $builder->setDebug($config->debug())->build();
    }
}
