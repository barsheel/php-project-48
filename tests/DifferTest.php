<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

final class DifferTest extends TestCase
{
    private function getFixture(string $filename): string
    {
        return (__DIR__ . "/fixtures/" . $filename);
    }

    public function testJSON(): void
    {
        $file1 = $this->getFixture("file3.json");
        $file2 = $this->getFixture("file4.json");

        $expectedResultStylish = file_get_contents(DifferTest::getFixture("expected_result_stylish"));
        $result = genDiff($file1, $file2, "stylish");
        $this -> assertEquals($expectedResultStylish, $result);

        $expectedResultPlain = file_get_contents(DifferTest::getFixture("expected_result_plain"));
        $result = genDiff($file1, $file2, "plain");
        $this -> assertEquals($expectedResultPlain, $result);

        $expectedResultJson = file_get_contents(DifferTest::getFixture("expected_result_json"));
        $result = genDiff($file1, $file2, "json");
        $this -> assertEquals($expectedResultJson, $result);
    }

    public function testYAML(): void
    {
        $file1 = $this->getFixture("file3.yml");
        $file2 = $this->getFixture("file4.yml");

        $expectedResultStylish = file_get_contents(DifferTest::getFixture("expected_result_stylish"));
        $result = genDiff($file1, $file2, "stylish");
        $this -> assertEquals($expectedResultStylish, $result);

        $expectedResultPlain = file_get_contents(DifferTest::getFixture("expected_result_plain"));
        $result = genDiff($file1, $file2, "plain");
        $this -> assertEquals($expectedResultPlain, $result);

        $expectedResultJson = file_get_contents(DifferTest::getFixture("expected_result_json"));
        $result = genDiff($file1, $file2, "json");
        $this -> assertEquals($expectedResultJson, $result);
    }
}
