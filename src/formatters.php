<?php

namespace PHP\Project48\Formatters;

use Exception;

use function PHP\Project48\Formatters\PlainFormatter\plainFormatter;
use function PHP\Project48\Formatters\StylishFormatter\stylishFormatter;
use function PHP\Project48\Formatters\JsonFormatter\jsonFormatter;

function format(array $array, string $formatter): string
{
    switch ($formatter) {
        case "stylish":
            return stylishFormatter($array);
        case "plain":
            return plainFormatter($array);
        case "json":
            return jsonFormatter($array);
        default:
            throw new Exception("No such formatter");
    }
}
