<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Transformers;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Dropelikeit\ResponseFactory\Contracts\Transformers\InputTransformer;
use Dropelikeit\ResponseFactory\Dtos\Services\StreamInput;
use Override;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 *
 * @template-implements InputTransformer<StreamInput>
 */
final class StreamInputToStringTransformer implements InputTransformer
{
    /**
     * @psalm-return non-empty-string
     */
    #[Override]
    public function transform(Input $toBeTransformed): string
    {
        $content = $toBeTransformed->getValue()->__toString();

        Assert::stringNotEmpty($content);

        return $content;
    }
}
