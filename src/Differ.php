<?php

/**
 * Logic functions of gendiff
 */

namespace Differ\Differ;

use Functional;

use function Differ\Differ\Parsers\parseFile;
use function Differ\Differ\Formatters\format;

/**
 * Compare two files and return difference
 *
 * @param string $pathToFile1
 * @param string $pathToFile2
 * @return string
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $fileArray1 = (parseFile($pathToFile1));
    $fileArray2 = (parseFile($pathToFile2));
    $diffArray = arrayDiffRecursive($fileArray1, $fileArray2);

    $output = format($diffArray, $format);

    
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
    $mergedArray = array_merge($fileArray1, $fileArray2);
    $sortedArrayKeys = \Functional\sort(
        array_keys($mergedArray),
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

            //if both are array
            if ($isArrayFirst && $isArraySecond) {
                $children = arrayDiffRecursive($elementFromFirst, $elementFromSecond);
                return ['key' => $key, "type" => "array", "children" => $children];
            }
            if ($isArrayFirst) {
                $elementFromFirst = transformAssociativeArray($fileArray1[$key]);
            } elseif ($isArraySecond) {
                $elementFromSecond = transformAssociativeArray($fileArray2[$key]);
            }
            if ($isExistsInFirst && $isExistsInSecond) {
                if ($elementFromFirst === $elementFromSecond) {
                    return ['key' => $key, "type" => "unchanged", "value" => $elementFromFirst];
                } else {
                    return ['key' => $key, "type" => "changed", "old_value" => $elementFromFirst, "new_value" => $elementFromSecond];
                }
            }
            if ($isExistsInFirst) {
                return ['key' => $key, "type" => "removed", "old_value" => $elementFromFirst];
            }
            if ($isExistsInSecond) {
                return ['key' => $key, "type" => "added", "new_value" => $elementFromSecond];
            }
        },
        $sortedArrayKeys
    );
    return $result;
}
/**
 * Transfrom array to internal form
 * @param array $array
 * @return array
 */
function transformAssociativeArray(array $array): array
{
    $sortedArrayKeys = \Functional\sort(
        array_keys($array),
        fn($first, $second) => strcmp($first, $second)
    );

    $result = array_map(
        function ($key) use ($array) {

            $element = $array[$key];

            //if both are array
            if (is_array($element)) {
                $children = transformAssociativeArray($element);
                return ['key' => $key, "type" => "array", "children" => $children];
            } else {
                return ['key' => $key, "type" => "unchanged", "value" => $element];
            }
        },
        $sortedArrayKeys
    );
    return $result;
}
