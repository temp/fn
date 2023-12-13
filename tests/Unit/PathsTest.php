<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\paths;

/** @covers \Fnc\paths */
final class PathsTest extends TestCase
{
    public function testPath(): void
    {
        $data = [
            'a' => ['b' => 2],
            'p' => [['q' => 3]],
        ];

        $result = paths([['a', 'b'], ['p', 0, 'q']], $data);

        $this->assertEquals([2, 3], $result);
    }

    public function testMissingNumericIndex(): void
    {
        $data = [
            'a' => ['b' => 2],
            'p' => [['q' => 3]],
        ];

        $result = paths([['a', 'b'], ['p', 1, 'q']], $data);

        $this->assertEquals([2, null], $result);
    }

    public function testMissingAssociativeIndex(): void
    {
        $data = [
            'a' => ['b' => 2],
            'p' => [['q' => 3]],
        ];

        $result = paths([['a', 'b'], ['p', 0, 'z']], $data);

        $this->assertEquals([2, null], $result);
    }

    public function testWrongType(): void
    {
        $data = [
            'a' => ['b' => 2],
            'p' => [['q' => 3]],
        ];

        $result = paths([['a', 'b', 'c']], $data);

        $this->assertEquals([null], $result);
    }
}
