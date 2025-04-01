<?php

namespace Php\Project48\Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Php\Project48\Gendiff\genDiff;

final class GendiffTest extends TestCase
{
    private function getFixture(string $filename): string
    {
        return (__DIR__ . "/fixtures/" . $filename);
    }
    public function testFlatJSON(): void
    {
        $file1 = $this->getFixture("file1.json");
        $file2 = $this->getFixture("file2.json");

        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result1"));
        $result = genDiff($file1, $file2);
        file_put_contents("output", $result);
        $this -> assertEquals($expectedResult, $result);

        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result2"));
        $result = genDiff($file2, $file1);
        file_put_contents("output", $result);
        $this -> assertEquals($expectedResult, $result);
    }

    public function testFlatYML(): void
    {
        $file1 = $this->getFixture("file1.yml");
        $file2 = $this->getFixture("file2.yml");

        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result1"));
        $result = genDiff($file1, $file2);
        file_put_contents("output", $result);
        $this -> assertEquals($expectedResult, $result);

        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result2"));
        $result = genDiff($file2, $file1);
        file_put_contents("output", $result);
        $this -> assertEquals($expectedResult, $result);
    }
}
