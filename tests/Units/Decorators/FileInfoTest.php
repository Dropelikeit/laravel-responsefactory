<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\Units\Decorators;

use Dropelikeit\ResponseFactory\Decorators\FileInfo;
use Dropelikeit\ResponseFactory\Dtos\Decorators\Mimetype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: FileInfo::class)]
#[UsesClass(className: Mimetype::class)]
final class FileInfoTest extends TestCase
{
    #[Test]
    #[DataProvider(methodName: 'dataProviderCanDetectMimetype')]
    public function canDetectMimetype(string $content, Mimetype $expectedMimetype): void
    {
        $detector = new FileInfo();

        $mimetype = $detector->fetchMimetypeByGivenString($content);

        $this->assertEquals($expectedMimetype, $mimetype);
    }

    public static function dataProviderCanDetectMimetype(): array
    {
        return [
            'json' => [
                '{"foo": "bar"}',
                Mimetype::createFromType('application/json'),
            ],
            'xml' => [
                '<?xml version="1.0" encoding="UTF-8"?>',
                Mimetype::createFromType('text/xml'),
            ],
            'text' => [
                'some text',
                Mimetype::createFromType('text/plain'),
            ],
        ];
    }
}
