<?php

namespace Php\Project48\Formatters\PlainFormatter;

function plainFormatter(array $inputArray): string
{
    $array = arrayCastValuesToStringWithQuotes($inputArray);
    return plainFormatterRecursive($array);
}

/**
 * Output array like string
 *
 * @param array $inputArray
 * @param string $parent            - name of current array key
 * @return string
 */
function plainFormatterRecursive(array $inputArray, string $parent = ""): string
{
    $output = [];

    foreach ($inputArray as $key => $signedElement) {
        $property = $parent ? "{$parent}.{$key}" : "{$key}";

        if (isset($signedElement['actual']) && is_array($signedElement['actual'])) {
            //if no changes at current element
            $output[] = plainFormatterRecursive($signedElement['actual'], $property);
        } elseif (isset($signedElement['old']) && isset($signedElement['new'])) {
            //if element was updated
            $oldValue = is_array($signedElement['old']) ? "[complex value]" : $signedElement['old'];
            $newValue = is_array($signedElement['new']) ? "[complex value]" : $signedElement['new'];
            $output[] = "Property '{$property}' was updated. From {$oldValue} to {$newValue}";
        } elseif (isset($signedElement['old'])) {
            //if element was removed
            $output[] = "Property '{$property}' was removed";
        } elseif (isset($signedElement['new'])) {
            //if element was added
            $element = $signedElement['new'];
            if ((is_array($signedElement['new']))) {
                $value = "[complex value]";
            } else {
                $value = $element;
            }
            $output[] = "Property '{$property}' was added with value: {$value}";
        }
    }


    return implode("\n", $output);
}

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
