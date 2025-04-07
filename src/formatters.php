<?php

namespace PHP\Project48\Formatters;

use Exception;

use function PHP\Project48\Formatters\PlainFormatter\plainFormatter;
use function PHP\Project48\Formatters\StylishFormatter\stylishFormatter;

function format(array $array, string $formatter): string
{
    switch ($formatter) {
        case "stylish":
            return stylishFormatter($array);
        case "plain":
            return plainFormatter($array);
        default:
            throw new Exception("No such formatter");
    }
}
