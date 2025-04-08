<?php

namespace Php\Project48\Formatters\PlainFormatter;

/**
 * convert diff array to plain-style text
 * @param array $inputArray
 * @return string
 */
function plainFormatter(array $inputArray): string
{
    $array = arrayCastValuesToStringWithQuotes($inputArray);
    return plainFormatterRecursive($array);
}

/**
 * internal recursive function which forms plain-style text
 *
 * @param array $inputArray
 * @param string $parent
 * @return string
 */
function plainFormatterRecursive(array $inputArray, string $parent = ""): string
{
    $output = [];

    foreach ($inputArray as $key => $signedElement) {
        $property = $parent ? "{$parent}.{$key}" : "{$key}";

        if (isset($signedElement['actualValue']) && is_array($signedElement['actualValue'])) {
            //if no changes at current element
            $output[] = plainFormatterRecursive($signedElement['actualValue'], $property);
        } elseif (isset($signedElement['oldValue']) && isset($signedElement['newValue'])) {
            //if element was updated
            $oldValue = is_array($signedElement['oldValue']) ? "[complex value]" : $signedElement['oldValue'];
            $newValue = is_array($signedElement['newValue']) ? "[complex value]" : $signedElement['newValue'];
            $output[] = "Property '{$property}' was updated. From {$oldValue} to {$newValue}";
        } elseif (isset($signedElement['oldValue'])) {
            //if element was removed
            $output[] = "Property '{$property}' was removed";
        } elseif (isset($signedElement['newValue'])) {
            //if element was added
            $element = $signedElement['newValue'];
            if ((is_array($signedElement['newValue']))) {
                $value = "[complex value]";
            } else {
                $value = $element;
            }
            $output[] = "Property '{$property}' was added with value: {$value}";
        }
    }


    return implode("\n", $output);
}

/**
 * сast all values in array to plain style - it's prepare diff array for output
 *
 * @param array $inputArray
 * @return array
 */
function arrayCastValuesToStringWithQuotes(array $inputArray): array
{
    return array_map(
        function ($elem) {
            if (is_array($elem)) {
                return arrayCastValuesToStringWithQuotes($elem);
            } elseif (is_bool($elem)) {
                return $elem ? "true" : "false";
            } elseif (is_null($elem)) {
                return "null";
            } else {
                return "'{$elem}'";
            }
        },
        $inputArray
    );
}
