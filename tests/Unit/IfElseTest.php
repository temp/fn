<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\always;
use function Fnc\ifElse;

#[CoversFunction('Fnc\ifElse')]
final class IfElseTest extends TestCase
{
    public function testString(): void
    {
        $ifElse = ifElse(always(true), always(true), always(false));

        $result = $ifElse(true);

        $this->assertTrue($result);
    }
}
