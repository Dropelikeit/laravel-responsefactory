<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Dtos\Services;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 *
 * @template T
 */
interface Input
{
    /**
     * @return T
     */
    public function getValue(): mixed;
}
