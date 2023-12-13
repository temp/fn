<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\apply;
use function max;

/** @covers \Fnc\apply */
final class ApplyTest extends TestCase
{
    public function testArrayValue(): void
    {
        $data = [1, 99, 30];
        $max = static function (...$list) {
            return max($list);
        };

        $result = apply($max, $data);

        $this->assertSame(99, $result);
    }

    public function testCurriedArrayValue(): void
    {
        $data = [1, 99, 30];
        $max = static function (...$list) {
            return max($list);
        };

        $apply = apply($max);

        $result = $apply($data);

        $this->assertSame(99, $result);
    }
}
