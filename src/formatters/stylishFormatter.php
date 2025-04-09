<?php

namespace Differ\Differ\Formatters\StylishFormatter;

/**
 * convert diff array to stylish-style text
 * @param array $inputArray
 * @return string
 */
function stylishFormatter(array $inputArray): string
{
    $array = arrayCastValuesToString($inputArray);
    return stylishFormatterRecursive($array);
}

/**
 * internal recursive function which forms stylish-style text
 *
 * @param  array   $inputArray
 * @param  integer $offsetLevel       - needs to construct indent
 * @return string
 */
function stylishFormatterRecursive(array $inputArray, int $offsetLevel = 0): string
{
    $PRINT_ARRAY_BASE_OFFSET = "  ";

    $parentOffset = str_repeat($PRINT_ARRAY_BASE_OFFSET, $offsetLevel * 2);
    $elementOffset = "{$parentOffset}{$PRINT_ARRAY_BASE_OFFSET}";
    $output = [];
    $prefix = "";

    foreach ($inputArray as $key => $signedElement) {
        foreach ($signedElement as $sign => $childElement) {
            switch ($sign) {
                case "actualValue":
                    $prefix = "  ";
                    break;
                case "oldValue":
                    $prefix = "- ";
                    break;
                case "newValue":
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
        ["{", ...$output, "{$parentOffset}}"]
    );
}

/**
 * сast all values in array to stylish style - it's prepare diff array for output
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
