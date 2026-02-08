<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Features;

use Dropelikeit\ResponseFactory\Contracts\Http\ResponseFactory as ResponseFactoryContract;
use Dropelikeit\ResponseFactory\Enums\SerializeTypeEnum;
use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use Dropelikeit\ResponseFactory\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ServiceProviderTest extends TestCase
{
    private readonly Application $application;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        $configRepository = $this
            ->getMockBuilder(className: Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository
            ->expects($this->once())
            ->method('set')
            ->with('responsefactory', [
                'serialize_null' => true,
                'serialize_type' => 'json',
                'debug' => false,
                'add_default_handlers' => true,
                'custom_handlers' => [],
            ]);

        $configRepository
            ->expects($this->exactly(6))
            ->method('get')
            ->willReturnOnConsecutiveCalls([], true, SerializeTypeEnum::JSON, false, true, []);

        Storage::shouldReceive('disk')
            ->andReturnSelf();

        Storage::shouldReceive('exists')
            ->once()
            ->with(__DIR__ . '/data/storage/framework/cache/data')
            ->andReturn(true);

        $this->application = new Application();
        $this->application->useStoragePath(__DIR__ . '/data/storage');

        $this->application->bind(abstract: 'config', concrete: fn () => $configRepository);

        $this->application->register(provider: ServiceProvider::class);
    }

    #[Test]
    public function canBuildResponseFactoryByIdFromConfiguredServiceProvider(): void
    {
        $responseFactory = $this->application->get('ResponseFactory');

        $this->assertInstanceOf(expected: ResponseFactoryContract::class, actual: $responseFactory);
    }

    #[Test]
    public function canBuildResponseFactoryByClassConfiguredServiceProvider(): void
    {
        $responseFactory = $this->application->get(ResponseFactory::class);

        $this->assertInstanceOf(expected: ResponseFactoryContract::class, actual: $responseFactory);
    }
}
