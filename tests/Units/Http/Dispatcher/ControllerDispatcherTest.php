<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Http\Dispatcher;

use Dropelikeit\ResponseFactory\Contracts\Http\ResponseFactory;
use Dropelikeit\ResponseFactory\Http\Attributes\HandledByResponseFactory;
use Dropelikeit\ResponseFactory\Http\Dispatcher\ControllerDispatcher;
use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Route;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(className: ControllerDispatcher::class)]
final class ControllerDispatcherTest extends TestCase
{
    private Container $container;
    private ResponseFactory $responseFactory;

    #[Override]
    public function setUp(): void
    {
        $this->container = $this
            ->getMockBuilder(className: Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseFactory = $this
            ->getMockBuilder(className: ResponseFactory::class)
            ->getMock();

        parent::setUp();
    }

    private function createRouteMock(): Route
    {
        $route = $this->getMockBuilder(className: Route::class)->disableOriginalConstructor()->getMock();
        $route->method('parametersWithoutNulls')->willReturn([]);

        return $route;
    }

    #[Test]
    public function dispatchReturnsResponseWhenControllerReturnsResponse(): void
    {
        $dispatcher = new ControllerDispatcher(container: $this->container, responseFactory: $this->responseFactory);

        $route = $this->createRouteMock();

        $controller = new class() {
            public function index(): Response
            {
                return new JsonResponse(['hello' => 'world']);
            }
        };

        $this->responseFactory
            ->expects($this->never())
            ->method('createFromArray');

        $this->responseFactory
            ->expects($this->never())
            ->method('create');

        $result = $dispatcher->dispatch(route: $route, controller: $controller, method: 'index');

        self::assertInstanceOf(Response::class, $result);
    }

    #[Test]
    public function dispatchReturnsResultWhenMethodHasNoAttribute(): void
    {
        $dispatcher = new ControllerDispatcher(container: $this->container, responseFactory: $this->responseFactory);

        $route = $this->createRouteMock();

        $controller = new class() {
            public function index(): array
            {
                return ['hello' => 'world'];
            }
        };

        $this->responseFactory
            ->expects($this->never())
            ->method('createFromArray');

        $this->responseFactory
            ->expects($this->never())
            ->method('create');

        $result = $dispatcher->dispatch(route: $route, controller: $controller, method: 'index');

        self::assertSame(['hello' => 'world'], $result);
    }

    #[Test]
    public function dispatchUsesResponseFactoryForArrayWhenAttributeIsPresent(): void
    {
        $dispatcher = new ControllerDispatcher(container: $this->container, responseFactory: $this->responseFactory);

        $route = $this->createRouteMock();

        $controller = new class() {
            #[HandledByResponseFactory]
            public function index(): array
            {
                return ['hello' => 'world'];
            }
        };

        $expectedResponse = new JsonResponse(['hello' => 'world']);

        $this->responseFactory
            ->expects($this->once())
            ->method('createFromArray')
            ->with(['hello' => 'world'])
            ->willReturn($expectedResponse);

        $this->responseFactory
            ->expects($this->never())
            ->method('create');

        $result = $dispatcher->dispatch(route: $route, controller: $controller, method: 'index');

        self::assertSame($expectedResponse, $result);
    }

    #[Test]
    public function dispatchUsesResponseFactoryForObjectWhenAttributeIsPresent(): void
    {
        $dispatcher = new ControllerDispatcher(container: $this->container, responseFactory: $this->responseFactory);

        $route = $this->createRouteMock();

        $responseObject = new stdClass();
        $responseObject->hello = 'world';

        $controller = new class($responseObject) {
            public function __construct(private readonly stdClass $responseObject)
            {
            }

            #[HandledByResponseFactory]
            public function index(): stdClass
            {
                return $this->responseObject;
            }
        };

        $expectedResponse = new JsonResponse(['hello' => 'world']);

        $this->responseFactory
            ->expects($this->never())
            ->method('createFromArray');

        $this->responseFactory
            ->expects($this->once())
            ->method('create')
            ->with($responseObject)
            ->willReturn($expectedResponse);

        $result = $dispatcher->dispatch(route: $route, controller: $controller, method: 'index');

        self::assertSame($expectedResponse, $result);
    }

    #[Test]
    public function dispatchHandlesInvalidControllerMethodGracefully(): void
    {
        $dispatcher = new ControllerDispatcher(container: $this->container, responseFactory: $this->responseFactory);

        $route = $this->createRouteMock();

        // Using an invalid class name that will cause ReflectionException
        $controller = new class() {
            public function __call(string $name, array $arguments): array
            {
                return ['data' => 'value'];
            }
        };

        $this->responseFactory
            ->expects($this->never())
            ->method('createFromArray');

        $this->responseFactory
            ->expects($this->never())
            ->method('create');

        // When ReflectionMethod fails, the result from parent dispatcher should be returned as-is
        $result = $dispatcher->dispatch(route: $route, controller: $controller, method: 'dynamicMethod');

        self::assertSame(['data' => 'value'], $result);
    }

    #[Test]
    public function dispatchReturnsResponseWhenAttributePresentButResultIsAlreadyResponse(): void
    {
        $dispatcher = new ControllerDispatcher(container: $this->container, responseFactory: $this->responseFactory);

        $route = $this->createRouteMock();

        $controller = new class() {
            #[HandledByResponseFactory]
            public function index(): Response
            {
                return new JsonResponse(['hello' => 'world']);
            }
        };

        $this->responseFactory
            ->expects($this->never())
            ->method('createFromArray');

        $this->responseFactory
            ->expects($this->never())
            ->method('create');

        $result = $dispatcher->dispatch(route: $route, controller: $controller, method: 'index');

        self::assertInstanceOf(Response::class, $result);
    }

    #[Test]
    public function dispatchUsesContainerFromParentToResolveMethodDependencies(): void
    {
        // This test ensures parent::__construct is called properly
        // by verifying that method dependencies are resolved via the container
        $dependencyMock = $this->getMockBuilder(\stdClass::class)->getMock();

        $this->container
            ->expects($this->once())
            ->method('make')
            ->with(\stdClass::class)
            ->willReturn($dependencyMock);

        $dispatcher = new ControllerDispatcher(container: $this->container, responseFactory: $this->responseFactory);

        $route = $this->createRouteMock();
        $route->method('signatureParameters')->willReturn([]);

        // Create a controller with a method that has a dependency
        $controller = new class() {
            public function index(\stdClass $dependency): array
            {
                return ['resolved' => 'dependency'];
            }
        };

        $result = $dispatcher->dispatch(route: $route, controller: $controller, method: 'index');

        self::assertIsArray($result);
    }

}
