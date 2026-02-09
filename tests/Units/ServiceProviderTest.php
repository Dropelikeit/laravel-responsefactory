<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units;

use Dropelikeit\ResponseFactory\Configuration\Configuration;
use Dropelikeit\ResponseFactory\Contracts\Services\MimeTypeDetector;
use Dropelikeit\ResponseFactory\Factories\Http\SerializerFactory;
use Dropelikeit\ResponseFactory\Http\Dispatcher\ControllerDispatcher;
use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use Dropelikeit\ResponseFactory\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: ServiceProvider::class)]
final class ServiceProviderTest extends TestCase
{
    private readonly MockObject&Application $application;
    private readonly MockObject&Repository $configRepository;
    private readonly MockObject&MimeTypeDetector $mimetypeDetector;
    private ?\Illuminate\Contracts\Container\Container $oldContainer;

    public function setUp(): void
    {
        $this->application = $this->createMock(Application::class);
        $this->configRepository = $this->createMock(Repository::class);
        $this->mimetypeDetector = $this->getMockBuilder(MimeTypeDetector::class)->getMock();

        $this->oldContainer = Container::getInstance();
        Container::setInstance($this->application);
    }

    #[Test]
    public function canRegister(): void
    {
        $this->configRepository
            ->expects(self::once())
            ->method('set')
            ->with('responsefactory', [
                'serialize_null' => true,
                'serialize_type' => 'json', // Contracts\Config::SERIALIZE_TYPE_XML
                'debug' => false,
                'add_default_handlers' => true,
                'custom_handlers' => [],
            ]);

        $this->configRepository
            ->expects(self::exactly(6))
            ->method('get')
            ->willReturnOnConsecutiveCalls([], true, 'json', false, true, []);

        $this->application
            ->expects(self::once())
            ->method('make')
            ->with('config')
            ->willReturn($this->configRepository);

        $this->application
            ->expects(self::exactly(1))
            ->method('get')
            ->willReturnOnConsecutiveCalls($this->configRepository, $this->mimetypeDetector);

        $this->application
            ->expects(self::once())
            ->method('storagePath')
            ->with('framework/cache/data')
            ->willReturn('my-storage');

        Storage::shouldReceive('exists')->once()->with('my-storage')->andReturn(false);
        Storage::shouldReceive('makeDirectory')->with('my-storage')->andReturn(true);

        $config = Configuration::fromArray([
            'serialize_null' => true,
            'cache_dir' => 'tmp',
            'serialize_type' => 'json',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [],
        ]);

        $this->application
            ->expects(self::once())
            ->method('singleton')
            ->with(ResponseFactory::class, static function (Application $app) use ($config): ResponseFactory {
                $mimetypeDetector = $app->get(MimeTypeDetector::class);

                return new ResponseFactory(
                    (new SerializerFactory())->getSerializer($config),
                    $config,
                    $mimetypeDetector
                );
            });

        $this->application
            ->expects(self::exactly(5))
            ->method('bind');

        $provider = new ServiceProvider($this->application);

        $provider->register();
    }

    #[Test]
    public function canLoadConfigAtBootingApp(): void
    {
        $this->application
            ->expects(self::once())
            ->method('configPath')
            ->with('responsefactory.php')
            ->willReturn('my/dir');

        $provider = new ServiceProvider($this->application);

        $provider->boot();
    }

    #[Test]
    public function canRegisterControllerDispatcher(): void
    {
        $this->configRepository
            ->expects(self::once())
            ->method('set')
            ->with('responsefactory', [
                'serialize_null' => true,
                'serialize_type' => 'json',
                'debug' => false,
                'add_default_handlers' => true,
                'custom_handlers' => [],
            ]);

        $this->configRepository
            ->expects(self::exactly(6))
            ->method('get')
            ->willReturnOnConsecutiveCalls([], true, 'json', false, true, []);

        $this->application
            ->expects(self::once())
            ->method('make')
            ->with('config')
            ->willReturn($this->configRepository);

        $this->application
            ->expects(self::exactly(1))
            ->method('storagePath')
            ->with('framework/cache/data')
            ->willReturn('my-storage');

        Storage::shouldReceive('exists')->once()->with('my-storage')->andReturn(true);

        $bindCalls = [];
        $this->application
            ->method('get')
            ->willReturnCallback(function (string $abstract) use (&$bindCalls) {
                if ($abstract === 'config') {
                    return $this->configRepository;
                }
                if ($abstract === MimeTypeDetector::class) {
                    return $this->mimetypeDetector;
                }
                return null;
            });

        $this->application
            ->method('bind')
            ->willReturnCallback(function (string $abstract, $concrete) use (&$bindCalls) {
                $bindCalls[] = $abstract;
            });

        $this->application
            ->expects(self::once())
            ->method('singleton');

        $provider = new ServiceProvider($this->application);
        $provider->register();

        // Verify that ControllerDispatcher was registered
        self::assertContains(ControllerDispatcherContract::class, $bindCalls);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Container::setInstance($this->oldContainer);
    }
}
