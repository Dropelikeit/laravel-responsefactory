<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class Item
{
    #[Serializer\Type('string')]
    private string $key = 'magic_number';

    #[Serializer\Type('integer')]
    private int $value = 12;

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
