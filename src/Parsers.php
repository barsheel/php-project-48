<?php

namespace Differ\Differ\Parsers;

use Exception;
use stdClass;
use Symfony\Component\Yaml\Yaml;

/**
 * Parse json or yaml file
 *
 * @param string $path
 * @throws \Exception
 * @return array
 */
function parseFile(string $path): array
{
    if (str_ends_with(strtolower($path), ".json")) {
        return parseJsonFile($path);
    }
    if (str_ends_with(strtolower($path), ".yaml") || str_ends_with(strtolower($path), ".yml")) {
        return parseYamlFile($path);
    }
    throw new Exception("no such file or wrong file format");
}

/**
 * Parse YAML file and return it in array form
 *
 * @param  string $path - filename
 * @return array
 */
function parseYamlFile(string $path): array
{
    $data = Yaml::parseFile(buildPath($path), Yaml::PARSE_OBJECT_FOR_MAP);
    return get_object_vars_recursive($data);
}

/**
 * Parse JSON file and return it in array form
 *
 * @param  string $path - filename
 * @throws Exception
 * @return array
 */
function parseJsonFile(string $path): array
{
    $buildedPath = buildPath($path);
    $fileContent = file_get_contents($buildedPath);
    if ($fileContent === false) {
        throw new Exception("Can't get contents");
    }
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
 * Summary of Differ\Differ\Parsers\buildPath
 *  * Convert filename to absolute path if its relative
 *
 * @param  string $path - path or filename
 * @return string absolute path
 * @throws \Exception
 */
function buildPath(string $path): string
{
    $realPath = realpath($path);
    if ($realPath === false) {
        throw new Exception("\nNo such file\n");
    } elseif (is_file($realPath)) {
        return $realPath;
    }
    $currentDirectory = dirname(__DIR__) . "/" . $path;
    if (is_file($currentDirectory)) {
        return $currentDirectory;
    }
    throw new Exception("\nNo such file\n");
}
