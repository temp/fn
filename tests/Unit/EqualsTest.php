<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use stdClass;

use function Fnc\equals;

use const NAN;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

#[CoversFunction('Fnc\equals')]
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

    public function testStringCurried(): void
    {
        $this->assertTrue(equals('a')('a'));
        $this->assertFalse(equals('a')('b'));
        $this->assertFalse(equals('a')(''));
        $this->assertFalse(equals('1')(1));
        $this->assertFalse(equals('1.0')(1.0));
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

    public function testListOrderMatters(): void
    {
        $this->assertFalse(equals([1, 2], [2, 1]));
        $this->assertFalse(equals([1, 1, 2], [1, 2, 2]));
        $this->assertFalse(equals([1, 2], [1, 2, 2]));
    }

    public function testAssociativeKeyOrderDoesNotMatter(): void
    {
        $this->assertTrue(equals(['a' => 1, 'b' => 2], ['b' => 2, 'a' => 1]));
    }

    public function testObjectAndArrayAreNotEqual(): void
    {
        $this->assertFalse(equals((object) ['a' => 1], ['a' => 1]));
        $this->assertFalse(equals(['a' => 1], (object) ['a' => 1]));
    }

    public function testObjectsOfDifferentClassesAreNotEqual(): void
    {
        $a = new Foo(123);
        $b = new OtherFoo(123);

        $this->assertFalse(equals($a, $b));
    }

    public function testClosuresAreComparedByIdentity(): void
    {
        $fn = static fn () => 1;

        $this->assertTrue(equals($fn, $fn));
        $this->assertFalse(equals($fn, static fn () => 1));
        $this->assertFalse(equals([$fn], [static fn () => 1]));
    }

    public function testCyclicObjects(): void
    {
        $a = new stdClass();
        $a->self = $a;
        $b = new stdClass();
        $b->self = $b;
        $c = new stdClass();
        $c->self = $c;
        $c->extra = 1;

        $this->assertTrue(equals($a, $b));
        $this->assertFalse(equals($a, $c));
    }

    public function testNan(): void
    {
        $this->assertTrue(equals(NAN, NAN));
        $this->assertTrue(equals([NAN], [NAN]));
        $this->assertFalse(equals(NAN, 0.0));
    }

    public function testCurriedComparisonsDoNotShareState(): void
    {
        $isEmptyObject = equals(new stdClass());

        $this->assertTrue($isEmptyObject(new stdClass()));
        $this->assertTrue($isEmptyObject(new stdClass()));

        $withProperty = new stdClass();
        $withProperty->a = 1;
        $this->assertFalse($isEmptyObject($withProperty));
    }
}

class Foo
{
    public function __construct(
        // only compared by equals() through an array cast
        // @phpstan-ignore property.onlyWritten
        private int $bar,
    ) {
    }
}

class OtherFoo
{
    public function __construct(
        // only compared by equals() through an array cast
        // @phpstan-ignore property.onlyWritten
        private int $bar,
    ) {
    }
}
