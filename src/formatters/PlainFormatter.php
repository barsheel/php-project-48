<?php

namespace Differ\Differ\Formatters\PlainFormatter;

/**
 * convert diff array to plain-style text
 * @param array $inputArray
 * @return string
 */
function plainFormatter(array $inputArray): string
{
    return recursive($inputArray);
}

/**
 * internal recursive function which forms plain-style text
 *
 * @param array $inputArray
 * @param string $parent
 * @return string
 */
function recursive(array $inputArray, string $parent = ""): string
{
    $output = array_reduce(
        $inputArray,
        function ($acc, $element) use ($parent) {
            $key = $element["key"];
            $property = ($parent !== "") ? "{$parent}.{$key}" : "{$key}";

            switch ($element["type"]) {
                case "unchanged":
                    return $acc;
                case "added":
                    $value = is_array($element["new_value"]) ? "[complex value]" : makeString($element["new_value"]);
                    $append = "Property '{$property}' was added with value: {$value}";
                    return [...$acc, $append];
                case "removed":
                    $value = is_array($element["old_value"]) ? "[complex value]" : makeString($element["old_value"]);
                    $append = "Property '{$property}' was removed";
                    return [...$acc, $append];
                case "changed":
                    $oldValue = is_array($element["old_value"]) ? "[complex value]" : makeString($element["old_value"]);
                    $newValue = is_array($element["new_value"]) ? "[complex value]" : makeString($element["new_value"]);
                    $append = "Property '{$property}' was updated. From {$oldValue} to {$newValue}";
                    return [...$acc, $append];
                case "array":
                    $append = recursive($element['children'], $property);
                    return [...$acc, $append];
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
