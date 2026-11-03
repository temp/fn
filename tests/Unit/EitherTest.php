<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\either;

#[CoversFunction('Fnc\either')]
final class EitherTest extends TestCase
{
    public function testEither(): void
    {
        $fn = either(static fn ($n) => $n < 0, static fn ($n) => $n > 10);

        $this->assertTrue($fn(-1));
        $this->assertTrue($fn(11));
        $this->assertFalse($fn(5));
    }
}
