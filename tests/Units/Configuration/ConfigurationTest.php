<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Configuration;

use Dropelikeit\ResponseFactory\Configuration\Configuration;
use Dropelikeit\ResponseFactory\Contracts\Configuration\CustomHandlerConfiguration;
use Dropelikeit\ResponseFactory\Exceptions\MissingRequiredItems;
use Dropelikeit\ResponseFactory\Exceptions\SerializeType;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: Configuration::class)]
#[UsesClass(className: MissingRequiredItems::class)]
#[UsesClass(className: SerializeType::class)]
final class ConfigurationTest extends TestCase
{
    /**
     * @param array{serialize_null: bool, cache_dir: string, serialize_type: string, debug: bool, add_default_handlers: bool, custom_handlers: array<int, CustomHandlerConfiguration>} $config
     */
    #[Test]
    #[DataProvider(methodName: 'dataProviderCanCreateConfig')]
    #[TestDox(text: 'can create config with config: $config, throws missing exception: $throwMissingException, throws wrong type exception: $throwWrongTypeException, expected error message: $expectedErrorMessage')]
    public function canCreateConfig(
        array $config,
        bool $throwMissingException,
        bool $throwWrongTypeException,
        string $expectedErrorMessage,
    ): void {
        if ($throwMissingException) {
            $this->expectException(MissingRequiredItems::class);
        }

        if ($throwWrongTypeException) {
            $this->expectException(SerializeType::class);
        }

        if (
            $throwMissingException
            || $throwWrongTypeException
        ) {
            $this->expectExceptionMessage($expectedErrorMessage);
            $this->expectExceptionCode(400);
        }

        $configTest = Configuration::fromArray($config);

        self::assertEquals($config['serialize_null'], $configTest->shouldSerializeNull());
        self::assertEquals(sprintf('%s%s', $config['cache_dir'], '/serializer/'), $configTest->getCacheDir());
        self::assertEquals($config['serialize_type'], $configTest->getSerializeType()->value);
        self::assertEquals($config['debug'], $configTest->debug());
        self::assertTrue($configTest->shouldAddDefaultHeaders());
        self::assertCount(0, $configTest->getCustomHandlers());
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function dataProviderCanCreateConfig(): array
    {
        return [
            'success' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                false,
                false,
                '',
            ],
            'missing_required_fields' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                true,
                false,
                'Missing required fields, please check your serializer-config. Missing fields "debug"',
            ],
            'wrong_serialize_type' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'yaml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                false,
                true,
                'Unknown given type "yaml" allowed types are "json" and "xml"',
            ],
            'missing_required_serialize_null_key' => [
                [
                    'cache_dir' => '/storage',
                    'serialize_type' => 'yaml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                true,
                false,
                'Missing required fields, please check your serializer-config. Missing fields "serialize_null"',
            ],
        ];
    }

    /**
     * @param array{serialize_null: bool, cache_dir: string, serialize_type: string, debug: bool, add_default_handlers: bool, custom_handlers: array<int, CustomHandlerConfiguration>} $config
     */
    #[Test]
    #[DataProvider('dataProviderCanNotCreateConfigBecauseInvalidArgumentExceptionThrows')]
    #[TestDox(text: 'fails to create config when $config is invalid, expecting error "$expectedError" with status code $statusCode')]
    public function canNotCreateConfigBecauseInvalidArgumentExceptionThrows(array $config, int $statusCode, string $expectedError): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode($statusCode);
        $this->expectExceptionMessage($expectedError);

        Configuration::fromArray($config);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function dataProviderCanNotCreateConfigBecauseInvalidArgumentExceptionThrows(): array
    {
        return [
            'serialize_null_not_a_boolean' => [
                [
                    'serialize_null' => 1,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                0,
                'Expected a boolean. Got: integer',
            ],
            'debug_is_not_a_boolean' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'debug' => 2,
                    'serialize_type' => 'json',
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                0,
                'Expected a boolean. Got: integer',
            ],
            'add_default_handler_is_not_a_boolean' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'debug' => false,
                    'add_default_handlers' => 2,
                    'custom_handlers' => [],
                ],
                0,
                'Expected a boolean. Got: integer',
            ],
            'serialize_type_is_not_json_or_xml' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'yaml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                400,
                'Unknown given type "yaml" allowed types are "json" and "xml"',
            ],
            'cache_dir_is_not_a_string' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => 123,
                    'serialize_type' => 'xml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                0,
                'Expected a string. Got: integer',
            ],
            'custom_handlers' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'xml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => '',
                ],
                0,
                'Expected an array. Got: string',
            ],
        ];
    }
}
