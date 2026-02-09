<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Http\Dispatcher;

use Dropelikeit\ResponseFactory\Contracts\Http\ResponseFactory;
use Dropelikeit\ResponseFactory\Http\Attributes\HandledByResponseFactory;
use Illuminate\Container\Container;
use Illuminate\Routing\ControllerDispatcher as BaseControllerDispatcher;
use Illuminate\Routing\Route;
use function is_array;
use Override;
use ReflectionException;
use ReflectionMethod;

use Symfony\Component\HttpFoundation\Response;

final class ControllerDispatcher extends BaseControllerDispatcher
{
    public function __construct(Container $container, private readonly ResponseFactory $responseFactory)
    {
        parent::__construct(container: $container);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function dispatch(Route $route, $controller, $method): mixed
    {
        $result = parent::dispatch(route: $route, controller: $controller, method: $method);
        if ($result instanceof Response) {
            return $result;
        }

        try {
            $reflectionMethod = new ReflectionMethod(objectOrMethod: $controller, method: $method);
        } catch (ReflectionException) {
            return $result;
        }

        $attributes = $reflectionMethod->getAttributes(HandledByResponseFactory::class);
        if ($attributes === []) {
            return $result;
        }

        if (is_array($result)) {
            return $this->responseFactory->createFromArray(jmsResponse: $result);
        }

        return $this->responseFactory->create(jmsResponse: $result);
    }
}
