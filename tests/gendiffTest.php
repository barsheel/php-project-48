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

    public function testJSON(): void
    {
        $file1 = $this->getFixture("file3.json");
        $file2 = $this->getFixture("file4.json");

        $expectedResultStylish = file_get_contents(GendiffTest::getFixture("expected_result3"));
        $result = genDiff($file1, $file2, "stylish");
        $this -> assertEquals($expectedResultStylish, $result);

        $expectedResultPlain = file_get_contents(GendiffTest::getFixture("expected_result4"));
        $result = genDiff($file1, $file2, "plain");
        $this -> assertEquals($expectedResultPlain, $result);
    }

    public function testYAML(): void
    {
        $file1 = $this->getFixture("file3.yml");
        $file2 = $this->getFixture("file4.yml");

        $expectedResultStylish = file_get_contents(GendiffTest::getFixture("expected_result3"));
        $result = genDiff($file1, $file2, "stylish");
        $this -> assertEquals($expectedResultStylish, $result);

        $expectedResultPlain = file_get_contents(GendiffTest::getFixture("expected_result4"));
        $result = genDiff($file1, $file2, "plain");
        $this -> assertEquals($expectedResultPlain, $result);
    }
}
