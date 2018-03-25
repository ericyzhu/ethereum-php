<?php

namespace Ethereum\Abi\Types\Interfaces;

interface TypeBytesInterface extends TypeInterface
{
    /**
     * @return int
     */
    public function getByteSize(): int;
}
