<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\tap;

#[CoversFunction('Fnc\tap')]
final class TapTest extends TestCase
{
    public function testTap(): void
    {
        $seen = null;
        $result = tap(static function ($value) use (&$seen): void {
            $seen = $value;
        }, 'value');

        $this->assertSame('value', $result);
        $this->assertSame('value', $seen);
    }
}
