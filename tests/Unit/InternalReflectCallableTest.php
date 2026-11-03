<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function Fnc\_reflectCallable;

#[CoversFunction('Fnc\_reflectCallable')]
final class InternalReflectCallableTest extends TestCase
{
    public function testCallable(): void
    {
        $this->assertSame('trim', _reflectCallable('trim')->getName());
    }

    public function testNonCallableThrows(): void
    {
        $this->expectException(RuntimeException::class);

        _reflectCallable('not a function');
    }
}
