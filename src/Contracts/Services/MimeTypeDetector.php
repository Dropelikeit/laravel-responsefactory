<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Services;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
interface MimeTypeDetector
{
    /**
     * @psalm-return non-empty-string
     */
    public function detect(Input $toDetect): string;
}
