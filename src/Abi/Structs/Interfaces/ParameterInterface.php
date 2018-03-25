<?php

namespace Ethereum\Abi\Structs\Interfaces;

/**
 *
 * @property string $name
 * @property string $type
 */
interface ParameterInterface
{
    public const CONTAINER_ARRAY = 0;
    public const CONTAINER_TUPLE = 1;

    /**
     * @param object $data
     */
    public function __construct(object $data);

    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data): string;

    /**
     * @param string $data
     * @return mixed
     */
    public function deserialize(string $data);
    /**
     * @return bool
     */
    public function isDynamic(): bool;
}