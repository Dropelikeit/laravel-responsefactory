<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Features;

use Dropelikeit\ResponseFactory\Contracts\Http\ResponseFactory as ResponseFactoryContract;
use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use Dropelikeit\ResponseFactory\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ServiceProviderTest extends TestCase
{
    private readonly Application $application;

    public function setUp(): void
    {
        parent::setUp();

        $configRepository = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository
            ->expects(self::once())
            ->method('set')
            ->with('responsefactory', [
                'serialize_null' => true,
                'serialize_type' => 'json',
                'debug' => false,
                'add_default_handlers' => true,
                'custom_handlers' => [],
            ]);

        $configRepository
            ->expects(self::exactly(6))
            ->method('get')
            ->willReturnOnConsecutiveCalls([], true, 'json', false, true, []);

        Storage::shouldReceive('exists')->once()->with(__DIR__ . '/data/storage/framework/cache/data')->andReturn(true);

        $this->application = new Application();
        $this->application->useStoragePath(__DIR__ . '/data/storage');

        $this->application->bind('config', fn () => $configRepository);

        $this->application->register(ServiceProvider::class);
    }

    #[Test]
    public function canBuildResponseFactoryByIdFromConfiguredServiceProvider(): void
    {
        $responseFactory = $this->application->get('ResponseFactory');

        $this->assertInstanceOf(ResponseFactoryContract::class, $responseFactory);
    }

    #[Test]
    public function canBuildResponseFactoryByClassConfiguredServiceProvider(): void
    {
        $responseFactory = $this->application->get(ResponseFactory::class);

        $this->assertInstanceOf(ResponseFactoryContract::class, $responseFactory);
    }
}
