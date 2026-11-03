<?php

declare(strict_types=1);

namespace FncTests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function array_map;
use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\prop;
use function Fnc\propSatisfies;
use function Fnc\remap;

#[CoversFunction('Fnc\remap')]
final class RemapTest extends TestCase
{
    public function testMovesKeysIntoNestedStructure(): void
    {
        $record = ['id' => 1, 'wheelhotel_id' => 'W1', 'wheelhotel_name' => 'Hotel', 'note' => 'x'];

        $result = remap(['wheelhotel' => ['id' => 'wheelhotel_id', 'name' => 'wheelhotel_name']], $record);

        $this->assertSame(['wheelhotel' => ['id' => 'W1', 'name' => 'Hotel'], 'id' => 1, 'note' => 'x'], $result);
    }

    public function testSameSourceKeyCanBeUsedTwice(): void
    {
        $result = remap(['hours' => ['mo' => 'mo_th', 'tu' => 'mo_th']], ['mo_th' => '8-17']);

        $this->assertSame(['hours' => ['mo' => '8-17', 'tu' => '8-17']], $result);
    }

    public function testMissingSourceKeyIsNull(): void
    {
        $this->assertSame(['a' => ['b' => null], 'x' => 1], remap(['a' => ['b' => 'missing']], ['x' => 1]));
    }

    public function testCallablesGetTheWholeRecordAndRemoveNothing(): void
    {
        $spec = [
            'position' => static fn (array $record) => [$record['lat'], $record['lng']],
            'brand' => ifElse(
                propSatisfies(isEmpty(), 'brand_id'),
                always(null),
                applySpec(['id' => prop('brand_id')]),
            ),
        ];

        $result = remap($spec, ['lat' => 1.5, 'lng' => 2.5, 'brand_id' => '']);

        $this->assertSame(
            ['position' => [1.5, 2.5], 'brand' => null, 'lat' => 1.5, 'lng' => 2.5, 'brand_id' => ''],
            $result,
        );
    }

    public function testStringsAreKeysEvenIfCallable(): void
    {
        $this->assertSame(['a' => 'value'], remap(['a' => 'trim'], ['trim' => 'value']));
    }

    public function testSpecKeysWin(): void
    {
        $result = remap(['address' => ['city' => 'city']], ['address' => 'Main St', 'city' => 'Berlin']);

        $this->assertSame(['address' => ['city' => 'Berlin']], $result);
    }

    public function testCurried(): void
    {
        $toDto = remap(['user' => ['id' => 'user_id']]);

        $result = array_map($toDto, [['user_id' => 1], ['user_id' => 2]]);

        $this->assertSame([['user' => ['id' => 1]], ['user' => ['id' => 2]]], $result);
    }

    public function testInvalidSpecValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        remap(['a' => 1], []);
    }
}
