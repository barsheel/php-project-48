<?php

namespace Differ\Differ\Parsers;

use Exception;
use stdClass;
use Symfony\Component\Yaml\Yaml;

/**
 * Parse YAML data and return it in array form
 *
 * @param  string $data
 * @return array
 */
function parseYaml(string $data): array
{
    return $data = Yaml::parse($data, Yaml::PARSE_OBJECT);
}

/**
 * Parse JSON data and return it in array form
 *
 * @param  string $data
 * @return array
 */
function parseJson(string $data): array
{
    return json_decode($data, true);
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
