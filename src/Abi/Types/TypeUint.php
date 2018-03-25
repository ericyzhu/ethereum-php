<?php

declare(strict_types=1);

namespace Ethereum\Abi\Types;

use Ethereum\Abi\Types\Abstracts\AbstractTypeInteger;
use Ethereum\Utils;

class TypeUint extends AbstractTypeInteger
{
    /**
     * @param string|int $data
     * @return string
     */
    public function hexize($data): string
    {
        return gmp_strval(gmp_init($data, 10), 16);
    }

    /**
     * @param string $data
     * @return string
     */
    public function dehexize(string $data)
    {
        return gmp_strval(gmp_init(Utils::removeHexPrefix($data), 16), 10);
    }

    /**
     * @param string|int $data
     * @return string
     */
    public function serialize($data): string
    {
        return str_pad($this->hexize($data), static::PADDING_BYTES * 2, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $data
     * @return string
     */
    public function deserialize(string $data)
    {
        return $this->dehexize($data);
    }
}
