<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Transformers;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;

/**
 * @template T
 */
interface InputTransformer
{
    /**
     * @psalm-param Input<T> $toBeTransformed
     * @psalm-return non-empty-string
     */
    public function transform(Input $toBeTransformed): string;
}
