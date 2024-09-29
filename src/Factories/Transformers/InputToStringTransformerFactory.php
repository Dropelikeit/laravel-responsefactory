<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Factories\Transformers;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Dropelikeit\ResponseFactory\Contracts\Factories\Transformers\InputToStringTransformerFactory as InputToStringTransformerFactoryContract;
use Dropelikeit\ResponseFactory\Contracts\Transformers\InputTransformer;
use Dropelikeit\ResponseFactory\Dtos\Services\StreamInput;
use Dropelikeit\ResponseFactory\Dtos\Services\StringInput;
use Dropelikeit\ResponseFactory\Exceptions\NotSupportedInputClassException;
use Dropelikeit\ResponseFactory\Transformers\StreamInputToStringTransformer;
use Dropelikeit\ResponseFactory\Transformers\StringInputToStringTransformer;

use function get_class;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class InputToStringTransformerFactory implements InputToStringTransformerFactoryContract
{
    public function factorize(Input $input): InputTransformer
    {
        $class = get_class($input);

        return match ($class) {
            StringInput::class => new StringInputToStringTransformer(),
            StreamInput::class => new StreamInputToStringTransformer(),
            default => throw NotSupportedInputClassException::create($class),
        };
    }
}
