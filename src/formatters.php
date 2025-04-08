<?php

namespace PHP\Project48\Formatters;

use Exception;

use function PHP\Project48\Formatters\PlainFormatter\plainFormatter;
use function PHP\Project48\Formatters\StylishFormatter\stylishFormatter;
use function PHP\Project48\Formatters\JsonFormatter\jsonFormatter;

/**
 * function calls formatter functions
 * @param array $inputArray
 * @param string $formatter - formatter's name
 * @return string
 */
function format(array $inputArray, string $formatter): string
{
    switch ($formatter) {
        case "stylish":
            return stylishFormatter($inputArray);
        case "plain":
            return plainFormatter($inputArray);
        case "json":
            return jsonFormatter($inputArray);
        default:
            return "\nNo such formatter\n";
    }
}
