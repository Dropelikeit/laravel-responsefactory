<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory;

use ArrayIterator;
use Dropelikeit\ResponseFactory\Tests\data\Units\Http\ResponseFactory\Response\Item;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 * @extends ArrayIterator<int, Item>
 */
final class Response extends ArrayIterator
{
    /**
     * @param array<int, Item> $items
     * @psalm-param list<Item> $items
     */
    private function __construct(array $items)
    {
        Assert::allIsInstanceOf($items, Item::class);
        Assert::isList($items);

        parent::__construct($items);
    }

    /**
     * @param array<int, Item> $items
     * @psalm-param list<Item> $items
     */
    public static function create(array $items): self
    {
        return new self($items);
    }
}
