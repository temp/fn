<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\path;

#[CoversFunction('Fnc\path')]
final class PathTest extends TestCase
{
    public function testPath(): void
    {
        $data = [
            'a' => ['b' => 2],
            'p' => [['q' => 3]],
        ];

        $result1 = path(['a', 'b'], $data);
        $result2 = path(['p', 0, 'q'], $data);

        $this->assertEquals(2, $result1);
        $this->assertEquals(3, $result2);
    }

    public function testWrongPath(): void
    {
        $data = [
            'a' => ['b' => 2],
            'p' => [['q' => 3]],
        ];

        $result = path(['p', 1, 'q'], $data);

        $this->assertNull($result);
    }
}
