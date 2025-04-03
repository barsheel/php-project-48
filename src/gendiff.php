<?php

/**
 * Logic functions of gendiff
 */

namespace Php\Project48\Gendiff;

use function PHP\Project48\Parsers\parseFile;

/**
 * Compare two files and return difference
 *
 * @param string $pathToFile1
 * @param string $pathToFile2
 * @return string
 */
function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $fileArray1 = arrayCastValuesToString(parseFile($pathToFile1));
    $fileArray2 = arrayCastValuesToString(parseFile($pathToFile2));
    $result = formatArrayToString(arrayDiffRecursive($fileArray1, $fileArray2));
    file_put_contents("output", $result);
    return $result;
}


function arrayDiffRecursive(array $fileArray1, array $fileArray2): array
{
    $resultArray = array_merge($fileArray1, $fileArray2);

    ksort($resultArray);

    $resultArray = array_reduce(
        array_keys($resultArray),
        function ($accArray, $key) use ($fileArray1, $fileArray2) {
            $existsInFirst = (array_key_exists($key, $fileArray1));
            $existsInSecond = (array_key_exists($key, $fileArray2));
            $elementFromFirst = $existsInFirst ? $fileArray1[$key] : null;
            $elementFromSecond = $existsInSecond ? $fileArray2[$key] : null;

            //if key exists in both arrays
            if ($existsInFirst && $existsInSecond) {
                //if values are both arrays
                if (is_array($elementFromFirst) && is_array($elementFromSecond)) {
                    $accArray["  {$key}"] = arrayDiffRecursive($elementFromFirst, $elementFromSecond);
                    return $accArray;
                }
                //if values are not array and values are equal
                if ($elementFromFirst === $elementFromSecond) {
                    $accArray["  {$key}"] = $elementFromFirst;
                    return $accArray;
                }
            }
            //if key exists in first
            if ($existsInFirst) {
                if (is_array($elementFromFirst)) {
                    $accArray["- {$key}"] = arrayDiffRecursive($elementFromFirst, $elementFromFirst);
                } else {
                    $accArray["- {$key}"] = $elementFromFirst;
                }
            } 
            //if key exists in second
            if($existsInSecond) {
                if (is_array($elementFromSecond)) {
                    $accArray["+ {$key}"] = arrayDiffRecursive($elementFromSecond, $elementFromSecond);
                } else {
                    $accArray["+ {$key}"] = $elementFromSecond;
                }
            }
            return $accArray;
        },
        []
    );
    
    return $resultArray;
}

/**
 * Output array like string
 *
 * @param  array   $inputArray
 * @param  integer $offset       - needs to construct indent
 * @return string
 */
function formatArrayToString(array $inputArray, int $offset = 0, string $parent = ""): string
{
    $PRINT_ARRAY_BASE_OFFSET = 2;

    $result = [];

    $braceOffset = str_repeat(" ", $offset);
    $elementOffset = str_repeat(" ", $offset + $PRINT_ARRAY_BASE_OFFSET);

    $result = array_reduce(
        array_keys($inputArray),
        function ($acc, $key) use ($inputArray, $offset, $elementOffset, $PRINT_ARRAY_BASE_OFFSET) {
            if (is_array($inputArray[$key])) {
                $acc[] = formatArrayToString($inputArray[$key], $offset + $PRINT_ARRAY_BASE_OFFSET * 2, "{$elementOffset}{$key}: ");
            } else {
                $acc[] = "{$elementOffset}{$key}: {$inputArray[$key]}";
            }
            return $acc;
        },
        []
    );

    return implode(
        "\n",
        ["{$parent}{",
        ...$result,
        "{$braceOffset}}"]
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
