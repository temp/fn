<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\filter;
use function Fnc\head;

/**
 * @covers \Fnc\filter
 */
final class FilterTest extends TestCase
{
    public function testFilter(): void
    {
        $list = [
            ['a' => 1, 'b' => 1],
            ['a' => 2, 'b' => 2],
            ['a' => 3, 'b' => 3],
        ];
        $aIsTwo = static function ($item) {
            return $item['a'] === 2 ? true : false;
        };
        $this->assertEquals([1 => ['a' => 2, 'b' => 2]], filter($aIsTwo, $list));
    }

    public function testFilterByValue(): void
    {
        $list3 = [1, 2];
        $valueIsOne = static function ($v) {
            return $v === 1 ? true : false;
        };
        $this->assertEquals(1, head(filter($valueIsOne, $list3)));
    }

    public function testFilterByKey(): void
    {
        $list4 = ['a' => 1, 'b' => 2];
        $valueIsOne = static function ($v, $key) { // phpcs:ignore
            return $key === 'a' ? true : false; // phpcs:ignore
        };

        $this->assertEquals(1, head(filter($valueIsOne, $list4)));
    }
}
