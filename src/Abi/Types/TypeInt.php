<?php

declare(strict_types=1);

namespace Ethereum\Abi\Types;

use Ethereum\Abi\Types\Abstracts\AbstractTypeInteger;
use Ethereum\Utils;

class TypeInt extends AbstractTypeInteger
{
    /**
     * @param string|int $data
     * @return string
     */
    public function hexize($data): string
    {
        if (gmp_sign($data) < 0) {
            $data = gmp_add($data, (gmp_sub(gmp_pow(2, $this->getBitSize()), 1)));
            $data = gmp_add($data, 1);
        }

        return gmp_strval($data, 16);
    }

    /**
     * @param string $data
     * @return string
     */
    public function dehexize(string $data)
    {
        $data = pack('H*', Utils::removeHexPrefix($data));
        $byteSize = $this->getBitSize() / 8;

        $offsetIndex = 0;
        $isNegative = (ord($data[$offsetIndex]) & 0x80) != 0x00;
        $number = gmp_init(ord($data[$offsetIndex++]) & 0x7F, 10);

        for ($i = 0; $i < $byteSize-1; $i++) {
            $number = gmp_or(gmp_mul($number, 0x100), ord($data[$offsetIndex++]));
        }

        if ($isNegative) {
            $number = gmp_sub($number, gmp_pow(2, $this->getBitSize() - 1));
        }

        return gmp_strval($number, 10);
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
