<?php

namespace Php\Project48\Formatters\StylishFormatter;

/**
 * Summary of Php\Project48\Formatters\StylishFormatter\stylishFormatter
 * @param array $inputArray
 * @return string
 */
function stylishFormatter(array $inputArray): string
{
    $array = arrayCastValuesToString($inputArray);
    return stylishFormatterRecursive($array);
}

/**
 * Output array like string
 *
 * @param  array   $inputArray
 * @param  integer $offsetLevel       - needs to construct indent
 * @param string $parent
 * @return string
 */
function stylishFormatterRecursive(array $inputArray, int $offsetLevel = 0, string $parent = ""): string
{
    $PRINT_ARRAY_BASE_OFFSET = "  ";

    $parentOffset = str_repeat($PRINT_ARRAY_BASE_OFFSET, $offsetLevel * 2);
    $elementOffset = "{$parentOffset}{$PRINT_ARRAY_BASE_OFFSET}";
    $output = [];
    $prefix = "";

    foreach ($inputArray as $key => $signedElement) {
        foreach ($signedElement as $sign => $childElement) {
            switch ($sign) {
                case "actual":
                    $prefix = "  ";
                    break;
                case "old":
                    $prefix = "- ";
                    break;
                case "new":
                    $prefix = "+ ";
                    break;
            }

            $value = is_array($childElement)
            ? stylishFormatterRecursive($childElement, $offsetLevel + 1)
            : $childElement;

            $output[] = "{$elementOffset}{$prefix}{$key}: {$value}";
        }
    }

    return implode(
        "\n",
        ["{$parent}{", ...$output, "{$parentOffset}}"]
    );
}

/**
 * Cast all values in array to string
 *
 * @param array $inputArray
 * @return array
 */
function arrayCastValuesToString(array $inputArray): array
{
    return array_map(
        function ($elem) {
            if (is_array($elem)) {
                return arrayCastValuesToString($elem);
            } elseif (is_bool($elem)) {
                return $elem ? "true" : "false";
            } elseif (is_null($elem)) {
                return "null";
            } else {
                return strval($elem);
            }
        },
        $inputArray
    );
}
