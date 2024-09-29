<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\data\Units\Factories\Transformers\InputToStringTransformerFactory;

use Dropelikeit\ResponseFactory\Contracts\Dtos\Services\Input;

final class UnkownInput implements Input
{
    public function getValue(): false
    {
        return false;
    }
}
