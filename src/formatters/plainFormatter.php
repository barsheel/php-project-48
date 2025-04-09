<?php

namespace Differ\Differ\Formatters\PlainFormatter;

/**
 * convert diff array to plain-style text
 * @param array $inputArray
 * @return string
 */
function plainFormatter(array $inputArray): string
{
    return plainFormatterRecursive($inputArray);
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

    $output = array_reduce(
        $inputArray,
        function ($acc, $element) use ($parent) {
            $key = $element["key"];
            $property = $parent ? "{$parent}.{$key}" : "{$key}";

            if ($element["type"] === "unchanged") {
                return $acc;
            }

            if ($element["type"] === "added") {
                $value = is_array($element["new_value"]) ? "[complex value]" : makeString($element["new_value"]);
                $acc[] = "Property '{$property}' was added with value: {$value}";
                return $acc;
            }

            if ($element["type"] === "removed") {
                $value = is_array($element["old_value"]) ? "[complex value]" : makeString($element["old_value"]);
                $acc[] = "Property '{$property}' was removed";
                return $acc;
            }

            if ($element["type"] === "changed") {
                $oldValue = is_array($element["old_value"]) ? "[complex value]" : makeString($element["old_value"]);
                $newValue = is_array($element["new_value"]) ? "[complex value]" : makeString($element["new_value"]);
                $acc[] = "Property '{$property}' was updated. From {$oldValue} to {$newValue}";
                return $acc;
            }

            if ($element["type"] === "array") {
                $acc[] = plainFormatterRecursive($element['children'], $property);
                return $acc;
            }
        },
        []
    );

    return implode("\n", $output);
}

/**
 * transform value to correct string form
 * @param mixed $elem
 * @return string
 */
function makeString(mixed $elem): string
{
    if (is_bool($elem)) {
        return $elem ? "true" : "false";
    } elseif (is_null($elem)) {
        return "null";
    } elseif (is_int($elem)) {
        return "{$elem}";
    } else {
        return "'{$elem}'";
    }
}
