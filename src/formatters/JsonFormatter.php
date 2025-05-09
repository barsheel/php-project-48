<?php

namespace Differ\Differ\Formatters\JsonFormatter;

const PRINT_ARRAY_BASE_OFFSET = "  ";

/**
 * convert diff array to json-style text
 * @param array $inputArray
 * @return string
 */
function jsonFormatter(array $inputArray): string
{
    $array = arrayCastValuesToJson($inputArray);
    return recursive($array);
}

/**
 * internal recursive function which forms json-style text
 *
 * @param  array   $inputArray
 * @param  integer $offsetLevel       - needs to construct indent
 * @return string
 */
function recursive(array $inputArray, int $offsetLevel = 0): string
{
    $parentOffset = str_repeat(PRINT_ARRAY_BASE_OFFSET, $offsetLevel);
    $elementOffset = str_repeat(PRINT_ARRAY_BASE_OFFSET, $offsetLevel + 1);

    $output = array_reduce(
        array_keys($inputArray),
        function ($acc, $key) use ($inputArray, $elementOffset, $offsetLevel) {
            $element = $inputArray[$key];
            if (!is_array($element)) {
                $append = "{$elementOffset}\"{$key}\": {$element}";
                return [...$acc, $append];
            } elseif (array_is_list($inputArray)) {
                $append = implode("", [$elementOffset, recursive($element, $offsetLevel + 1)]);
                return [...$acc, $append];
            } else {
                $append = implode("", [$elementOffset, "\"{$key}\": ", recursive($element, $offsetLevel + 1)]);
                return [...$acc, $append];
            }
        },
        []
    );

    $outputStr = implode(",\n", $output);

    if (array_is_list($inputArray)) {
        return implode("", ["[\n" , $outputStr , "\n{$parentOffset}]"]);
    } else {
        return implode("", ["{\n" , $outputStr , "\n{$parentOffset}}"]);
    }
}

/**
 * сast all values in array to json style - it's prepare diff array for output
 *
 * @param array $inputArray
 * @return array
 */
function arrayCastValuesToJson(array $inputArray): array
{
    return array_map(
        function ($elem) {
            if (is_array($elem)) {
                return arrayCastValuesToJson($elem);
            } elseif (is_bool($elem)) {
                return $elem ? "true" : "false";
            } elseif (is_null($elem)) {
                return "null";
            } elseif (is_int($elem)) {
                return $elem;
            } else {
                return "\"{$elem}\"";
            }
        },
        $inputArray
    );
}
