<?php

namespace Php\Project48\Formatters\JsonFormatter;

/**
 * Summary of Php\Project48\Formatters\StylishFormatter\stylishFormatter
 * @param array $inputArray
 * @return string
 */
function jsonFormatter(array $inputArray): string
{
    $array = arrayCastValuesToJson($inputArray);
    return jsonFormatterRecursive($array);
}

/**
 * Output array like string
 *
 * @param  array   $inputArray
 * @param  integer $offsetLevel       - needs to construct indent
 * @return string
 */
function jsonFormatterRecursive(array $inputArray, int $offsetLevel = 0): string
{
    $PRINT_ARRAY_BASE_OFFSET = "  ";

    $parentOffset = str_repeat($PRINT_ARRAY_BASE_OFFSET, $offsetLevel);
    $elementOffset = "{$parentOffset}{$PRINT_ARRAY_BASE_OFFSET}";
    $output = [];
    $prefix = "";

    foreach ($inputArray as $key => $signedElement) {
            $childElement = $inputArray[$key];
            $value = is_array($childElement)
            ? jsonFormatterRecursive($childElement, $offsetLevel + 1)
            : $childElement;

            $output[] = "{$elementOffset}\"{$key}\": {$value}";
    }

    $outputStr = implode(",\n", $output);

    return "{\n" . $outputStr . "\n{$parentOffset}}";
/*    return implode(
        ",\n",
        ["{", ...$output, "{$parentOffset}}"]
    );
    */
}

/**
 * Cast all values in array to string
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
            } else {
                return "\"{$elem}\"";
            }
        },
        $inputArray
    );
}
