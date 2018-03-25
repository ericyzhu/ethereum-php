<?php

namespace Ethereum\Abi\Types\Abstracts;

use Ethereum\Abi\Types\Interfaces\TypeInterface;
use Ethereum\Utils;

abstract class AbstractType implements TypeInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function hexizeWithPrefix($data): string
    {
        return Utils::ensureHexPrefix($this->hexize($data));
    }

}
