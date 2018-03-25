<?php

namespace Ethereum\Abi\Structs;

/**
 *
 * @property string $name
 * @property string $type
 * @property array $components
 */
class FunctionInput extends Parameter
{
    /**
     * @var array
     */
    protected $properties = [
        'name'       => 'string',
        'type'       => 'string',
        'components' => 'array',
    ];

    /**
     * @var array
     */
    protected $required = [
        'name',
        'type',
    ];
}