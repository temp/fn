<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\complement;
use function Fnc\isEmpty;

#[CoversFunction('Fnc\complement')]
final class ComplementTest extends TestCase
{
    public function testComplement(): void
    {
        $isNotEmpty = complement(isEmpty());

        $this->assertTrue($isNotEmpty('foo'));
    }
}
