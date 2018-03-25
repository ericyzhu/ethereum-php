<?php

namespace Ethereum\Abi\Structs;

/**
 *
 * @property string $name
 * @property string $type
 */
class FunctionOutput extends Parameter
{
    /**
     * @var array
     */
    protected $properties = [
        'name'       => 'string',
        'type'       => 'string',
    ];

    /**
     * @var array
     */
    protected $required = [
        'name',
        'type',
    ];
}