<?php

namespace Ethereum\Abi\Types;

use Ethereum\Abi\Types\Abstracts\AbstractType;
use Ethereum\Abi\Types\Interfaces\TypeStringInterface;
use Ethereum\Utils;

class TypeString extends AbstractType implements TypeStringInterface
{
    /**
     * @param string|int $data
     * @return string
     */
    public function hexize($data): string
    {
        return unpack('H*', $data())[1];
    }

    /**
     * @param string $data
     * @return string
     */
    public function dehexize(string $data)
    {
        return pack('H*', Utils::removeHexPrefix($data));
    }

    /**
     * @param string $data
     * @return string
     */
    public function serialize($data): string
    {
        $length = strlen($data);
        $paddingBytes = (int)ceil($length / static::PADDING_BYTES) * static::PADDING_BYTES;
        return (new TypeUint())->serialize($length) . str_pad($this->hexize($data), $paddingBytes * 2, '0', STR_PAD_RIGHT);
    }

    /**
     * @param string $data
     * @return string
     */
    public function deserialize(string $data)
    {
        $length = (new TypeUint())->deserialize(substr($data, 0, 64));
        $data   = substr($data, 64, $length * 2);
        return $this->dehexize($data);
    }

    /**
     * @return bool
     */
    public function isDynamic(): bool
    {
        return true;
    }
}
