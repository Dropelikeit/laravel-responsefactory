<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Decorators;

use Dropelikeit\ResponseFactory\Dtos\Decorators\Mimetype;

interface MimetypeFromFileInformationDetector
{
    /**
     * @psalm-param non-empty-string $content
     */
    public function fetchMimetypeByGivenString(string $content): Mimetype;
}
