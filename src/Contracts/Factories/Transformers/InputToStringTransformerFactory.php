<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Factories\Transformers;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;
use Dropelikeit\ResponseFactory\Contracts\Transformers\InputTransformer;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
interface InputToStringTransformerFactory
{
    public function factorize(Input $input): InputTransformer;
}
