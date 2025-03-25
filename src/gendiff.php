<?php

/**
 * Logic functions of gendiff
 */

namespace Php\Project48\Gendiff;

use Exception;
use stdClass;

const PRINT_ARRAY_BASE_OFFSET = 2;

/**
 * Convert filename in absolute path
 *
 * @param string $path - path or filename
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

    $currentDirectory = dirname(__DIR__). "/" . $path;
    if (is_file($currentDirectory)) {
        return $currentDirectory;
    }
    throw new Exception("\nNo such file\n");
}

/**
 * Parse JSON file and return it in array form
 *
 * @param string $path - filename
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
 * @param stdClass $data
 * @return array
 */
function get_object_vars_recursive(stdClass $data) : array
{
    $elements = get_object_vars($data);
    return array_map(
        fn($item) => ($item instanceof stdClass) ? get_object_vars_recursive($item) : $item,
        $elements
    );
}

/**
 * Print array recursively
 *
 * @param array $arrayToPrint
 * @param integer $offset - needs to construct indent
 * @return void
 */
function printArray(array $arrayToPrint, int $offset = 0): void
{
    $braceOffset = str_repeat(" ", $offset);
    $elementOffset = str_repeat(" ", $offset + PRINT_ARRAY_BASE_OFFSET);

    print_r("{$braceOffset}{\n");

    foreach ($arrayToPrint as $key => $value) {
        if (is_array($value)) {
            print_r("{$elementOffset}{$key}");
            print_r(":\n");
            printArray($value, $offset + PRINT_ARRAY_BASE_OFFSET * 2);
            break;
        }
        else if (is_bool($value)) {
            $value = $value ? "true" : "false";
        }
        print_r("{$elementOffset}{$key}: {$value}\n");
    }

    print_r("{$braceOffset}}\n");
}
