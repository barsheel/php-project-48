<?php

namespace Differ\Differ\Files;

use Exception;

use function PHPUnit\Framework\throwException;

/**
 * Read file content
 * @param string $pathToFile - absolute or relative
 * @return string
 */
function getContent(string $pathToFile): string
{
    $absolutePath = buildAbsolutePath($pathToFile);
    $content = file_get_contents($absolutePath);
    if ($content === false) {
        throw new Exception("Can't get file content");
    }
    return $content;
}

/**
 * Check is it JSON filename
 * @param string $path
 * @return bool
 */
function isJsonFilename(string $path)
{
    return str_ends_with(strtolower($path), ".json") ? true : false;
}

/**
 * Check is it YAML filename
 * @param string $path
 * @return bool
 */
function isYamlFilename(string $path)
{
    return str_ends_with(strtolower($path), ".yml")
            || str_ends_with(strtolower($path), ".yaml")
            ? true
            : false;
}

/**
 * Convert filename to absolute path if its relative
 *
 * @param  string $path - path or filename
 * @return string absolute path
 * @throws \Exception
 */
function buildAbsolutePath(string $path): string
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
