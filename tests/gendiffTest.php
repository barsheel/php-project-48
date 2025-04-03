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
        $this -> assertEquals($expectedResult, $result);

        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result2"));
        $result = genDiff($file2, $file1);
        $this -> assertEquals($expectedResult, $result);
    }

    public function testNestedJSON(): void
    {
        $file1 = $this->getFixture("file3.json");
        $file2 = $this->getFixture("file4.json");
        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result3"));
        $result = genDiff($file1, $file2);
        $this -> assertEquals($expectedResult, $result);
    }

    public function testFlatYML(): void
    {
        $file1 = $this->getFixture("file1.yml");
        $file2 = $this->getFixture("file2.yml");

        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result1"));
        $result = genDiff($file1, $file2);
        $this -> assertEquals($expectedResult, $result);

        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result2"));
        $result = genDiff($file2, $file1);
        $this -> assertEquals($expectedResult, $result);
    }
    
    public function testNestedYAML(): void
    {
        $file1 = $this->getFixture("file3.yml");
        $file2 = $this->getFixture("file4.yml");
        $expectedResult = file_get_contents(GendiffTest::getFixture("expected_result3"));
        $result = genDiff($file1, $file2);
        $this -> assertEquals($expectedResult, $result);
    }
}
