<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use stdClass;

use function Fnc\equals;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

/** @covers \Fnc\equals */
final class EqualsTest extends TestCase
{
    public function testString(): void
    {
        $this->assertTrue(equals('a', 'a'));
        $this->assertFalse(equals('a', 'b'));
        $this->assertFalse(equals('a', ''));
        $this->assertFalse(equals('1', 1));
        $this->assertFalse(equals('1.0', 1.0));
    }

    public function testInt(): void
    {
        $this->assertTrue(equals(22, 22));
        $this->assertFalse(equals(22, 33));
        $this->assertFalse(equals(22, 0));
        $this->assertFalse(equals(22, 22.0));
        $this->assertFalse(equals(1, true));
    }

    public function testFloat(): void
    {
        $this->assertTrue(equals(22.0, 22.0));
        $this->assertFalse(equals(22.0, 33.0));
        $this->assertFalse(equals(22.0, 0.0));
        $this->assertFalse(equals(22, 22.0));
        $this->assertfalse(equals(1.0, true));
    }

    public function testBool(): void
    {
        $this->assertTrue(equals(true, true));
        $this->assertFalse(equals(false, true));
        $this->assertFalse(equals(true, false));
        $this->assertFalse(equals(true, 1));
        $this->assertFalse(equals(true, 1.0));
    }

    public function testArray(): void
    {
        $this->assertTrue(equals([1, 2, 3], [1, 2, 3]));
        $this->assertTrue(equals([1, 'a', 'b' => [2, 3, 'c']], [1, 'a', 'b' => [2, 3, 'c']]));
        $this->assertFalse(equals([1, 2, 3], []));
        $this->assertFalse(equals([1], 1));
        $this->assertFalse(equals([1], [2]));
        $this->assertFalse(equals(['b' => 3, 'c' => 5], ['b' => 3]));
    }

    public function testObject(): void
    {
        $a = new Foo(123);
        $b = new Foo(123);
        $c = new Foo(234);
        $d = new stdClass();
        $d->bar = 123;

        $this->assertTrue(equals($a, $b));
        $this->assertFalse(equals($a, $c));
        $this->assertFalse(equals($a, $d));
    }
}

class Foo
{
    private $bar; // phpcs:ignore

    public function __construct($bar) // phpcs:ignore
    {
        $this->bar = $bar;
    }
}
