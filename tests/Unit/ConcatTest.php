<?php

declare(strict_types=1);

namespace FncTests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\concat;

#[CoversFunction('Fnc\concat')]
final class ConcatTest extends TestCase
{
    public function testNumericArrayValues(): void
    {
        $a = [1, 2, 3];
        $b = [1, 2, 4];
        $c = concat($a);
        $this->assertEquals([1, 2, 3, 1, 2, 4], concat($a, $b));
        $this->assertEquals([1, 2, 3, 1, 2, 4], $c($b));
        $this->assertEquals([1, 2], concat([1, 2], []));
    }

    public function testKeysAreNotPreserved(): void
    {
        $this->assertSame([1, 'c', 1], concat([1, 'b' => 'c'], [1]));
        $this->assertSame([1, 'c', 'd'], concat([1, 'b' => 'c'], ['b' => 'd']));
        $this->assertSame([1, 2], concat([5 => 1], [9 => 2]));
    }

    public function testStrings(): void
    {
        $this->assertSame('abcd', concat('ab', 'cd'));
        $this->assertSame('abcd', concat('ab')('cd'));
    }

    public function testMixedTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        concat('ab', ['c']);
    }
}
