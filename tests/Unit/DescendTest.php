<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\descend;
use function Fnc\prop;

#[CoversFunction('Fnc\descend')]
final class DescendTest extends TestCase
{
    public function testDescend(): void
    {
        $byAge = descend(prop('age'));

        $this->assertSame(1, $byAge(['age' => 1], ['age' => 2]));
        $this->assertSame(0, $byAge(['age' => 1], ['age' => 1]));
        $this->assertSame(-1, $byAge(['age' => 2], ['age' => 1]));
    }
}
