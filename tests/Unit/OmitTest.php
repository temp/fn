<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\omit;

#[CoversFunction('Fnc\omit')]
final class OmitTest extends TestCase
{
    public function testArrayValue(): void
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];

        $result = omit(['b', 'd', 'f'], $data);

        $this->assertEquals(['a' => 1, 'c' => 3, 'e' => 5], $result);
    }

    public function testCurriedArrayValue(): void
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];

        $omit = omit(['b', 'd', 'f']);

        $result = $omit($data);

        $this->assertEquals(['a' => 1, 'c' => 3, 'e' => 5], $result);
    }
}
