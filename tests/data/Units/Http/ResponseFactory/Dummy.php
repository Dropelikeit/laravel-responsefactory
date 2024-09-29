<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory;

use Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory\Response\Item;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class Dummy
{
    #[Serializer\Type('integer')]
    private int $amount = 12;

    #[Serializer\Type('string')]
    private string $text = 'Hello World!';

    /**
     * @var array<int, Item>|null
     * @psalm-var list<Item>|null
     */
    #[Serializer\Type('array<Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Response\Item>')]
    public ?array $items = null;

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return array<int, Item>|null
     */
    public function getItems(): ?array
    {
        return $this->items;
    }
}
