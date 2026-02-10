<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Http\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class HandledByResponseFactory
{
}
