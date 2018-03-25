<?php

namespace Ethereum\Abi\Structs;
use Ethereum\Types\HashCollection;

/**
 *
 * @property string $name
 * @property string $type
 * @property bool $indexed
 * @property array $components
 */
class EventInput extends Parameter
{
    /**
     * @var array
     */
    protected $properties = [
        'name'       => 'string',
        'type'       => 'string',
        'indexed'    => 'boolean',
        'components' => 'array',
    ];

    /**
     * @var array
     */
    protected $required = [
        'name',
        'type',
        'indexed',
    ];
}