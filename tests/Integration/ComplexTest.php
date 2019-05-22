<?php

declare(strict_types=1);

namespace FnTests\Integration;

use PHPUnit\Framework\TestCase;
use function Fn\always;
use function Fn\arr;
use function Fn\compose;
use function Fn\converge;
use function Fn\drop;
use function Fn\ifElse;
use function Fn\isEmpty;
use function Fn\join;
use function Fn\merge;
use function Fn\omit;
use function Fn\pipe;
use function Fn\prop;
use function Fn\take;

final class ComplexTest extends TestCase
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
                arr([
                    'contact' => arr([
                        'id' => prop('contact_id'),
                        'name' => prop('contact_name'),
                    ]),
                    'unloading_point' => ifElse(
                        compose(isEmpty(), prop('unloading_point_code')),
                        null,
                        arr([
                            'code' => prop('unloading_point_code'),
                            'description' => prop('unloading_point_description'),
                        ]),
                    ),
                    'creation_date' => converge(
                        join(' '),
                        [
                            pipe(prop('creation_date_date'), take(10)),
                            pipe(prop('creation_date_time'), drop(11)),
                        ]
                    ),
                    'invalid' => ifElse(
                        compose(isEmpty(), prop('invalid_id')),
                        null,
                        arr([
                            'id' => prop('invalid_id'),
                            'value' => prop('invalid_value'),
                        ]),
                        ),
                    'valid' => ifElse(
                        compose(isEmpty(), prop('valid_id')),
                        null,
                        arr([
                            'id' => prop('valid_id'),
                            'value' => prop('valid_value'),
                        ]),
                        ),
                    'nested' => arr([
                        'first' => arr([
                            'a' => always('foo'),
                            'b' => always(456),
                        ]),
                        'second' => arr([
                            'a' => always('bar'),
                            'b' => always(567),
                        ]),
                    ]),
                    'null' => always(null),
                    'string' => always('foo'),
                    'int' => always(789),
                    'float' => always(890.12),
                ]),
                omit([
                    'contact_id', 'contact_name',
                    'unloading_point_code', 'unloading_point_description',
                    'invalid_id', 'invalid_value',
                    'valid_id', 'valid_value',
                    'creation_date_date', 'creation_date_time',
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
