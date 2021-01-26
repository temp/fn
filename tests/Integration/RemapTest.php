<?php

declare(strict_types=1);

namespace FncTests\Integration;

use PHPUnit\Framework\TestCase;
use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\compose;
use function Fnc\converge;
use function Fnc\drop;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\join;
use function Fnc\merge;
use function Fnc\omit;
use function Fnc\pipe;
use function Fnc\prop;
use function Fnc\take;
use function Fnc\unapply;

// phpcs:disable Generic.PHP.ForbiddenFunctions.FoundWithAlternative

final class RemapTest extends TestCase
{
    public function testComplex(): void
    {
        $data = [
            'contact_id' => 123,
            'contact_name' => 'abc',
            'unloading_point_code' => 234,
            'unloading_point_description' => 'def',
            'invalid_id' => '',
            'invalid_value' => 'hello',
            'valid_id' => 'foo',
            'valid_value' => '',
            'creation_date_date' => '2019-01-01 00:00:00.000',
            'creation_date_time' => '1700-01-01 10:11:12.000',
        ];

        $remap = converge(
            merge(),
            [
                applySpec([
                    'contact' => [
                        'id' => prop('contact_id'),
                        'name' => prop('contact_name'),
                    ],
                    'unloading_point' => ifElse(
                        compose(isEmpty(), prop('unloading_point_code')),
                        always(null),
                        applySpec([
                            'code' => prop('unloading_point_code'),
                            'description' => prop('unloading_point_description'),
                        ]),
                    ),
                    'creation_date' => converge(
                        unapply(join(' ')),
                        [
                            pipe(prop('creation_date_date'), take(10)),
                            pipe(prop('creation_date_time'), drop(11)),
                        ]
                    ),
                    'invalid' => ifElse(
                        compose(isEmpty(), prop('invalid_id')),
                        always(null),
                        applySpec([
                            'id' => prop('invalid_id'),
                            'value' => prop('invalid_value'),
                        ]),
                    ),
                    'valid' => ifElse(
                        compose(isEmpty(), prop('valid_id')),
                        always(null),
                        applySpec([
                            'id' => prop('valid_id'),
                            'value' => prop('valid_value'),
                        ]),
                    ),
                    'nested' => [
                        'first' => [
                            'a' => always('foo'),
                            'b' => always(456),
                        ],
                        'second' => [
                            'a' => always('bar'),
                            'b' => always(567),
                        ],
                    ],
                    'null' => always(null),
                    'string' => always('foo'),
                    'int' => always(789),
                    'float' => always(890.12),
                ]),
                omit([
                    'contact_id',
                    'contact_name',
                    'unloading_point_code',
                    'unloading_point_description',
                    'invalid_id',
                    'invalid_value',
                    'valid_id',
                    'valid_value',
                    'creation_date_date',
                    'creation_date_time',
                ]),
            ]
        );

        $result = $remap($data);

        $this->assertEquals([
            'contact' => [
                'id' => 123,
                'name' => 'abc',
            ],
            'unloading_point' => [
                'code' => 234,
                'description' => 'def',
            ],
            'valid' => [
                'id' => 'foo',
                'value' => '',
            ],
            'invalid' => null,
            'nested' => [
                'first' => [
                    'a' => 'foo',
                    'b' => 456,
                ],
                'second' => [
                    'a' => 'bar',
                    'b' => 567,
                ],
            ],
            'creation_date' => '2019-01-01 10:11:12.000',
            'null' => null,
            'string' => 'foo',
            'int' => 789,
            'float' => 890.12,
        ], $result);
    }
}
