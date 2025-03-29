<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Transformers;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Dropelikeit\ResponseFactory\Contracts\Transformers\InputTransformer;
use Dropelikeit\ResponseFactory\Dtos\Services\StringInput;
use Override;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 *
 * @template-implements InputTransformer<StringInput>
 */
final class StringInputToStringTransformer implements InputTransformer
{
    /**
     * @psalm-return non-empty-string
     */
    #[Override]
    public function transform(Input $toBeTransformed): string
    {
        return $toBeTransformed->getValue();
    }
}
