<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Enums;

enum SerializeTypeEnum: string
{
    case JSON = 'json';
    case XML = 'xml';
}
