<?php

namespace Differ\Differ\Formatters;

use function Differ\Differ\Formatters\PlainFormatter\plainFormatter;
use function Differ\Differ\Formatters\StylishFormatter\stylishFormatter;
use function Differ\Differ\Formatters\JsonFormatter\jsonFormatter;

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
