<?php

declare(strict_types=1);

namespace FncTests\Integration;

use PHPUnit\Framework\TestCase;
use function Fnc\applySpec;
use function Fnc\pipe;
use function Fnc\prop;
use function Fnc\sum;

final class PipeApplySpecSumTest extends TestCase
{
    public function testComplex(): void
    {
        $data = [
            'a' => 2,
            'b' => 3,
            'c' => 4,
            'd' => 5,
        ];

        $fn = pipe(
            applySpec([
                prop('a'),
                prop('b'),
                prop('c'),
            ]),
            sum(),
        );

        $result = $fn($data);

        $this->assertEquals(9, $result);
    }
}
