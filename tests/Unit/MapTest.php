<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\map;
use function strtoupper;

/**
 * @covers \Fnc\map
 */
final class MapTest extends TestCase
{
    public function testInt(): void
    {
        $data = [1, 2, 3];

        $result = map(
            static function ($i) {
                return $i * 2;
            },
            $data
        );

        $this->assertSame([2, 4, 6], $result);
    }

    public function testCurriedInt(): void
    {
        $data = [1, 2, 3];

        $map = map(static function ($i) {
            return $i * 2;
        });

        $result = $map($data);

        $this->assertSame([2, 4, 6], $result);
    }

    public function testString(): void
    {
        $data = ['abc' => 'def'];

        $result = map(
            static function ($i) {
                return strtoupper($i);
            },
            $data
        );

        $this->assertSame(['abc' => 'DEF'], $result);
    }

    public function testCurriedString(): void
    {
        $data = ['abc' => 'def'];

        $map = map(static function ($i) {
            return strtoupper($i);
        });

        $result = $map($data);

        $this->assertSame(['abc' => 'DEF'], $result);
    }
}
