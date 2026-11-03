<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\always;
use function Fnc\ifElse;
use function is_string;
use function trim;

#[CoversFunction('Fnc\ifElse')]
final class IfElseTest extends TestCase
{
    public function testString(): void
    {
        $ifElse = ifElse(always(true), always(true), always(false));

        $result = $ifElse(true);

        $this->assertTrue($result);
    }

    public function testNullBranchesReturnArgument(): void
    {
        $isString = static fn ($value) => is_string($value);
        $trimStrings = ifElse($isString, static fn ($value) => trim($value), null);
        $onlyStrings = ifElse($isString, null, static fn ($value) => null);

        $this->assertSame('x', $trimStrings(' x '));
        $this->assertSame(5, $trimStrings(5));
        $this->assertSame(' x ', $onlyStrings(' x '));
        $this->assertNull($onlyStrings(5));
    }
}
