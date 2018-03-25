<?php

namespace Ethereum\Abi\Types\Interfaces;

interface TypeIntegerInterface extends TypeInterface
{
    /**
     * @return int
     */
    public function getBitSize(): int;
}
