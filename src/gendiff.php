<?php

/**
 * Logic functions of gendiff
 */

namespace Php\Project48\Gendiff;

use Exception;
use stdClass;

/**
 * Compare two flat-json files and return difference
 *
 * @param string $pathToFile1
 * @param string $pathToFile2
 * @return string
 */
function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $fileArray1 = arrayCastValuesToString(parseJsonFile(buildPath($pathToFile1)));
    $fileArray2 = arrayCastValuesToString(parseJsonFile(buildPath($pathToFile2)));

    $resultArray = array_merge($fileArray1, $fileArray2);
    ksort($resultArray);

    $resultArray = array_reduce(
        array_keys($resultArray),
        function ($accArray, $key) use ($fileArray1, $fileArray2) {
            if ((array_key_exists($key, $fileArray1)) && (array_key_exists($key, $fileArray2))) {
                if ($fileArray1[$key] === $fileArray2[$key]) {
                    $accArray[] = "    {$key}: {$fileArray1[$key]}";
                } else {
                    $accArray[] = "  - {$key}: {$fileArray1[$key]}";
                    $accArray[] = "  + {$key}: {$fileArray2[$key]}";
                }
            } elseif (array_key_exists($key, $fileArray1)) {
                $accArray[] = "  - {$key}: {$fileArray1[$key]}";
            } else {
                $accArray[] = "  + {$key}: {$fileArray2[$key]}";
            }
            return $accArray;
        },
        []
    );

    return implode("\n", ["{", ...$resultArray, "}"]);
}

/**
 * Convert filename in absolute path
 *
 * @param  string $path - path or filename
 * @return string absolute path
 */
function buildPath(string $path): string
{
    $realPath = realpath($path);
    print_r($realPath);
    if (is_file($realPath)) {
        return $realPath;
    }

    $pathFilesDirectory = dirname(__DIR__) . "/files/" . $path;
    if (is_file($pathFilesDirectory)) {
        return $pathFilesDirectory;
    }

    $currentDirectory = dirname(__DIR__) . "/" . $path;
    if (is_file($currentDirectory)) {
        return $currentDirectory;
    }
    throw new Exception("\nNo such file\n");
}

/**
 * Parse JSON file and return it in array form
 *
 * @param  string $path - filename
 * @return array
 */
function parseJsonFile(string $path): array
{
    $path = buildPath($path);
    $fileContent = file_get_contents($path);
    $data = json_decode($fileContent);
    return get_object_vars_recursive($data);
}


/**
 * Convert stdClass objects to array
 *
 * @param  stdClass $data
 * @return array
 */
function get_object_vars_recursive(stdClass $data): array
{
    $elements = get_object_vars($data);
    return array_map(
        fn($item) => ($item instanceof stdClass) ? get_object_vars_recursive($item) : $item,
        $elements
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
            } else {
                return strval($elem);
            }
        },
        $inputArray
    );
}


/**
 * Output array like string
 *
 * @param  array   $inputArray
 * @param  integer $offset       - needs to construct indent
 * @return string
 */
function arrayToString(array $inputArray, int $offset = 0): string
{
    $PRINT_ARRAY_BASE_OFFSET = 2;

    $result = [];

    $braceOffset = str_repeat(" ", $offset);
    $elementOffset = str_repeat(" ", $offset + $PRINT_ARRAY_BASE_OFFSET);

    $result = array_reduce(
        array_keys($inputArray),
        function ($acc, $key) use ($inputArray, $offset, $elementOffset, $PRINT_ARRAY_BASE_OFFSET) {
            if (is_array($inputArray[$key])) {
                $acc[] = "{$elementOffset}{$key}:";
                $acc[] = arrayToString($inputArray[$key], $offset + $PRINT_ARRAY_BASE_OFFSET * 2);
            } else {
                $acc[] = "{$elementOffset}{$key}: {$inputArray[$key]}";
            }
            return $acc;
        },
        []
    );

    return implode(
        "\n",
        ["{$braceOffset}{",
        ...$result,
        "{$braceOffset}}"]
    );
}
