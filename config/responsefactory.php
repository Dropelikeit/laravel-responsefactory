<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory;

use Dropelikeit\ResponseFactory\Enums\SerializeTypeEnum;

return [
    'serialize_null' => true,
    'serialize_type' => SerializeTypeEnum::JSON->value,
    'debug' => false,
    'add_default_handlers' => true,
    'custom_handlers' => [],
];
