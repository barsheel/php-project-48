<?php

/**
 * Logic functions of gendiff
 */

namespace Differ\Differ;

use Functional;
use Exception;

use function Differ\Differ\Formatters\format;
use function Differ\Differ\Parsers\parseYaml;
use function Differ\Differ\Parsers\parseJson;
use function Differ\Differ\Files\getContent;
use function Differ\Differ\Files\isJsonFilename;
use function Differ\Differ\Files\isYamlFilename;

/**
 * Compare two files and return difference
 *
 * @param string $pathToFile1
 * @param string $pathToFile2
 * @return string
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $file1Content = getContent($pathToFile1);
    $file2Content = getContent($pathToFile2);

    if (isJsonFilename($pathToFile1) && isJsonFilename($pathToFile2)) {
        $file1AST = parseJson($file1Content);
        $file2AST = parseJson($file2Content);
    } elseif (isYamlFilename($pathToFile1) && isYamlFilename($pathToFile2)) {
        $file1AST = parseYaml($file1Content);
        $file2AST = parseYaml($file2Content);
    } else {
        throw new Exception("not a JSON or YAML file");
    }

    $diffAST = arrayDiffRecursive($file1AST, $file2AST);

    $output = format($diffAST, $format);

    return $output;
}


/**
 * internal recursive function
 * @param array $fileArray1
 * @param array $fileArray2
 * @return array
 */
function arrayDiffRecursive(array $fileArray1, array $fileArray2): array
{
    $diffASTKeys = array_keys(array_merge($fileArray1, $fileArray2));
    $sortedDiffASTKeys = \Functional\sort(
        $diffASTKeys,
        fn($first, $second) => strcmp($first, $second)
    );

    $result = array_map(
        function ($key) use ($fileArray1, $fileArray2) {
            $isExistsInFirst = (array_key_exists($key, $fileArray1));
            $isExistsInSecond = (array_key_exists($key, $fileArray2));
            $elementFromFirst = $isExistsInFirst ? $fileArray1[$key] : null;
            $elementFromSecond = $isExistsInSecond ? $fileArray2[$key] : null;
            $isArrayFirst = is_array($elementFromFirst);
            $isArraySecond = is_array($elementFromSecond);

            //if both elements are array - calculate difference
            if ($isArrayFirst && $isArraySecond) {
                $children = arrayDiffRecursive($elementFromFirst, $elementFromSecond);
                return ['key' => $key, "type" => "array", "children" => $children];
            }

            //If only one element is array - transform it in internal form
            $oldValue = $isArrayFirst
                ? arrayDiffRecursive($elementFromFirst, $elementFromFirst)
                : $elementFromFirst;
            $newValue = $isArraySecond
                ? arrayDiffRecursive($elementFromSecond, $elementFromSecond)
                : $elementFromSecond;

            //if added or removed
            if (!$isExistsInSecond) {
                return ['key' => $key, "type" => "removed", "old_value" => $oldValue];
            } elseif (!$isExistsInFirst) {
                return ['key' => $key, "type" => "added", "new_value" => $newValue];
            }

            //if values exist in both arrays
            if ($elementFromFirst === $elementFromSecond) {
                return ['key' => $key, "type" => "unchanged", "value" => $oldValue];
            } else {
                return ['key' => $key, "type" => "changed", "old_value" => $oldValue, "new_value" => $newValue];
            }
        },
        $sortedDiffASTKeys
    );
    return $result;
}
