<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Configuration;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
interface CustomHandlerConfiguration
{
    public function getDirection(): int;

    /**
     * @psalm-return non-empty-string
     */
    public function getTypeName(): string;

    /**
     * @psalm-return non-empty-string
     */
    public function getFormat(): string;

    public function getCallable(): callable;
}
