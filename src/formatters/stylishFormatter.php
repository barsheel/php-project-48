<?php

namespace Differ\Differ\Formatters\StylishFormatter;

const PRINT_ARRAY_BASE_OFFSET = "  ";

/**
 * convert diff array to stylish-style text
 * @param array $input
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
 * @param  mixed   $input
 * @param  integer $offsetLevel       - needs to construct indent
 * @return string
 */
function stylishFormatterRecursive(mixed $input, int $offsetLevel = 0): string
{
    //if got value
    if (!is_array($input)) {
        return $input;
    }

    //if got indexed array
    $parentOffset = str_repeat(PRINT_ARRAY_BASE_OFFSET, $offsetLevel * 2);
    $elementOffset = str_repeat(PRINT_ARRAY_BASE_OFFSET, $offsetLevel * 2 + 1);
    $result = [];

    $result = array_reduce(
        $input,
        function ($acc, $item) use ($elementOffset, $offsetLevel) {
            $key = $item["key"];
            switch ($item["type"]) {
                case "unchanged":
                    $acc[] = implode([$elementOffset, "  ", $item["key"], ": ", stylishFormatterRecursive($item["value"], $offsetLevel + 1)]);
                    return $acc;
                case "added":
                    $acc[] = implode([$elementOffset, "+ ", $item["key"], ": ", stylishFormatterRecursive($item["new_value"], $offsetLevel + 1)]);
                    return $acc;
                case "removed":
                    $acc[] = implode([$elementOffset, "- ", $item["key"], ": ", stylishFormatterRecursive($item["old_value"], $offsetLevel + 1)]);
                    return $acc;
                case "changed":
                    $acc[] = implode([$elementOffset, "- ", $item["key"], ": ", stylishFormatterRecursive($item["old_value"], $offsetLevel + 1)]);
                    $acc[] = implode([$elementOffset, "+ ", $item["key"], ": ", stylishFormatterRecursive($item["new_value"], $offsetLevel + 1)]);
                    return $acc;
                case "array":
                    $acc[] = implode([$elementOffset, "  ", $item["key"], ": ", stylishFormatterRecursive($item["children"], $offsetLevel + 1)]);
                    return $acc;
            }
            return $acc;
        },
        []
    );

    return implode(
        "\n",
        ["{", ...$result, "{$parentOffset}}"]
    );
}

/**
 * сast all values in array to stylish style - it's prepare diff array for output
 *
 * @param array $input
 * @return array
 */
function arrayCastValuesToString(array $input): array
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
        $input
    );
}
