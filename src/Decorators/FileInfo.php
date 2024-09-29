<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Decorators;

use Dropelikeit\ResponseFactory\Contracts\Decorators\MimetypeFromFileInformationDetector;
use Dropelikeit\ResponseFactory\Dtos\Decorators\Mimetype;
use const FILEINFO_MIME_TYPE;

use finfo;

final readonly class FileInfo implements MimetypeFromFileInformationDetector
{
    private finfo $fileInformation;

    public function __construct()
    {
        $this->fileInformation = new finfo(FILEINFO_MIME_TYPE);
    }

    /**
     * @psalm-param non-empty-string $content
     */
    public function fetchMimetypeByGivenString(string $content): Mimetype
    {
        return Mimetype::createFromType($this->fileInformation->buffer($content));
    }
}
