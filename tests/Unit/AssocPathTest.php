<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\assocPath;

#[CoversFunction('Fnc\assocPath')]
final class AssocPathTest extends TestCase
{
    public function testCreatesMissingLevels(): void
    {
        $this->assertSame(['a' => ['b' => ['c' => 1]]], assocPath(['a', 'b', 'c'], 1, []));
    }

    public function testKeepsSiblings(): void
    {
        $input = ['a' => ['b' => 1, 'x' => 2], 'y' => 3];

        $this->assertSame(['a' => ['b' => 5, 'x' => 2], 'y' => 3], assocPath(['a', 'b'], 5, $input));
    }

    public function testReplacesScalarOnTheWay(): void
    {
        $this->assertSame(['a' => ['b' => 1]], assocPath(['a', 'b'], 1, ['a' => 'scalar']));
    }

    public function testEmptyPathReturnsValue(): void
    {
        $this->assertSame('value', assocPath([], 'value', ['a' => 1]));
    }
}
