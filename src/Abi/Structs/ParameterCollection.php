<?php

namespace Ethereum\Abi\Structs;

use Ethereum\Abi\Serializer;
use Ethereum\Collection;
use Ethereum\Abi\Structs\Interfaces\ParameterInterface;
use InvalidArgumentException;

class ParameterCollection extends Collection
{
    /**
     * ParameterCollection constructor.
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        foreach ($array as $item) {
            if ($item instanceof ParameterInterface) {
                $this->data[] = $item;
            } else {
                throw new InvalidArgumentException('The array item must be an instance of AbiParameter');
            }
        }
    }

    /**
     * @return string
     */
    public function getStringTypes()
    {
        return implode(',', array_map(function (ParameterInterface $val) {
            return $val->type;
        }, $this->data));
    }

    /**
     * @param array $arguments
     * @return string
     */
    public function serialize(array $arguments = []): string
    {
        return Serializer::serialize($this, $arguments);
    }

    /**
     * @param string $data
     * @return array
     */
    public function deserialize(string $data): array
    {
        return Serializer::deserialize($this, $data);
    }
}