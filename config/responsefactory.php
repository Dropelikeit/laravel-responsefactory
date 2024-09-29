<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory;

use Dropelikeit\ResponseFactory\Contracts\Configuration\Configuration;

return [
    'serialize_null' => true,
    'serialize_type' => Configuration::SERIALIZE_TYPE_JSON, // Configuration::SERIALIZE_TYPE_XML
    'debug' => false,
    'add_default_handlers' => true,
    'custom_handlers' => [],
];
