<?php

namespace Differ\Differ\Formatters\StylishFormatter;

const PRINT_ARRAY_BASE_OFFSET = "  ";

/**
 * convert diff array to stylish-style text
 * @param array $inputArray
 * @return string
 */
function stylishFormatter(array $inputArray): string
{
    $array = arrayCastValuesToString($inputArray);
    return recursive($array);
}

/**
 * internal recursive function which forms stylish-style text
 *
 * @param  mixed   $input
 * @param  integer $offsetLevel       - needs to construct indent
 * @return string
 */
function recursive(mixed $input, int $offsetLevel = 0): string
{
    //if got value
    if (!is_array($input)) {
        return $input;
    }

    //if got indexed array
    $parentOffset = str_repeat(PRINT_ARRAY_BASE_OFFSET, $offsetLevel * 2);
    $elementOffset = str_repeat(PRINT_ARRAY_BASE_OFFSET, $offsetLevel * 2 + 1);


    $result = array_reduce(
        $input,
        function ($acc, $item) use ($elementOffset, $offsetLevel) {
            $key = $item["key"];
            switch ($item["type"]) {
                case "unchanged":
                    $append = implode(
                        [$elementOffset, "  ", $item["key"], ": ", recursive($item["value"], $offsetLevel + 1)]
                    );
                    return [...$acc, $append];
                case "added":
                    $append = implode(
                        [$elementOffset, "+ ", $item["key"], ": ", recursive($item["new_value"], $offsetLevel + 1)]
                    );
                    return [...$acc, $append];
                case "removed":
                    $append = implode(
                        [$elementOffset, "- ", $item["key"], ": ", recursive($item["old_value"], $offsetLevel + 1)]
                    );
                    return [...$acc, $append];
                case "changed":
                    $append1 = implode(
                        [$elementOffset, "- ", $item["key"], ": ", recursive($item["old_value"], $offsetLevel + 1)]
                    );
                    $append2 = implode(
                        [$elementOffset, "+ ", $item["key"], ": ", recursive($item["new_value"], $offsetLevel + 1)]
                    );
                    return [...$acc, $append1, $append2];
                case "array":
                    $append = implode(
                        [$elementOffset, "  ", $item["key"], ": ", recursive($item["children"], $offsetLevel + 1)]
                    );
                    return [...$acc, $append];
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
