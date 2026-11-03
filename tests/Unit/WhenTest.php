<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\when;
use function is_string;

#[CoversFunction('Fnc\when')]
final class WhenTest extends TestCase
{
    public function testWhen(): void
    {
        $trimStrings = when(is_string(...), 'trim');

        $this->assertSame('x', $trimStrings(' x '));
        $this->assertSame(5, $trimStrings(5));
    }
}
