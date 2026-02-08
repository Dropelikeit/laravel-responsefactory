<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Factories\Http;

use Dropelikeit\ResponseFactory\Contracts\Configuration\Configuration;
use JMS\Serializer\SerializerInterface;

interface SerializerFactory
{
    public function getSerializer(Configuration $config): SerializerInterface;
}
