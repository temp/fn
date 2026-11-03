<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\ascend;
use function Fnc\prop;
use function Fnc\sortWith;

#[CoversFunction('Fnc\ascend')]
final class AscendTest extends TestCase
{
    public function testAscend(): void
    {
        $byAge = ascend(prop('age'));

        $this->assertSame(-1, $byAge(['age' => 1], ['age' => 2]));
        $this->assertSame(0, $byAge(['age' => 1], ['age' => 1]));
        $this->assertSame(1, $byAge(['age' => 2], ['age' => 1]));
    }

    public function testWithSortWith(): void
    {
        $this->assertSame([1, 2, 3], sortWith([ascend(static fn ($n) => $n)], [3, 1, 2]));
    }
}
