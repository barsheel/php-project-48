<?php

namespace PHP\Project48\Parsers;

use Exception;
use stdClass;
use Symfony\Component\Yaml\Yaml;

//ебануть надо чтобы парсило в данные, а конвертация в строки пусть будет в гендифе

//пусть будет парс джейсон файл

/**
 * parse json or yaml file
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
    return Yaml::parseFile(buildPath($path));
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
 * Convert filename to absolute path if its relative
 *
 * @param  string $path - path or filename
 * @return string absolute path
 */
function buildPath(string $path): string
{
    $realPath = realpath($path);
    if (is_file($realPath)) {
        return $realPath;
    }
    $currentDirectory = dirname(__DIR__) . "/" . $path;
    if (is_file($currentDirectory)) {
        return $currentDirectory;
    }
    throw new Exception("\nNo such file\n");
}
