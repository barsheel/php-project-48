<?php

namespace Php\Project48\Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Php\Project48\Gendiff\genDiff;

final class GendiffTest extends TestCase
{
    public function testFlatJSON(): void
    {
            $expectedResult = file_get_contents(__DIR__ . "/fixtures/expectedResult1");
            $result = genDiff("file1.json", "file2.json");
            file_put_contents("output", $result);
            $this -> assertEquals($expectedResult, $result);
    }
}
