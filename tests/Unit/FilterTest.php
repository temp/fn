<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\curry2;
use function Fnc\filter;
use function Fnc\head;

#[CoversFunction('Fnc\filter')]
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
            return $item['a'] === 2;
        };
        $this->assertEquals([1 => ['a' => 2, 'b' => 2]], filter($aIsTwo, $list));
    }

    public function testFilterByValue(): void
    {
        $list3 = [1, 2];
        $valueIsOne = static function ($v) {
            return $v === 1;
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

    public function testFilterWithInternalFunction(): void
    {
        $this->assertSame([0 => 1, 2 => '3'], filter('is_numeric', [1, 'a', '3']));
    }

    public function testFilterWithInternalFunctionWithOptionalParameter(): void
    {
        // trim() has an optional second parameter that must not receive the key
        $this->assertSame([1 => 'x'], filter('trim', [' ', 'x']));
    }

    public function testCurriedCallbackReceivesKey(): void
    {
        $evenKey = curry2(static fn ($v, $k) => $k % 2 === 0);

        $this->assertSame([0 => 'a', 2 => 'c'], filter($evenKey, ['a', 'b', 'c', 'd']));
    }
}
