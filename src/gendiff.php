<?php

/**
 * Logic functions of gendiff
 */

namespace Php\Project48\Gendiff;

use Exception;

use function PHP\Project48\Parsers\parseFile;
use function PHP\Project48\Formatters\format;

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

    file_put_contents("output", $output);
    return $output;
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
                    $accArray["{$key}"]["actual"] = arrayDiffRecursive($elementFromFirst, $elementFromSecond);
                    return $accArray;
                }
                //if values are not array and values are equal
                if ($elementFromFirst === $elementFromSecond) {
                    $accArray["{$key}"]["actual"] = $elementFromFirst;
                    return $accArray;
                }
            }
            //if key exists in first
            if ($existsInFirst) {
                if (is_array($elementFromFirst)) {
                    $accArray["{$key}"]["old"] = arrayDiffRecursive($elementFromFirst, $elementFromFirst);
                } else {
                    $accArray["{$key}"]["old"] = $elementFromFirst;
                }
            }
            //if key exists in second
            if ($existsInSecond) {
                if (is_array($elementFromSecond)) {
                    $accArray["{$key}"]["new"] = arrayDiffRecursive($elementFromSecond, $elementFromSecond);
                } else {
                    $accArray["{$key}"]["new"] = $elementFromSecond;
                }
            }
            return $accArray;
        },
        []
    );

    return $resultArray;
}
