<?php

namespace Ethereum\Abi\Types;

use Ethereum\Abi\Types\Abstracts\AbstractTypeBytes;
use Ethereum\Utils;

class TypeBytes extends AbstractTypeBytes
{
    /**
     * @param string|int $data
     * @return string
     */
    public function hexize($data): string
    {
        $output = '';
        foreach (array_values(unpack('C*',  $data)) as $code)  {
            $hex = dechex($code);
            if ((strlen($hex) % 2) !== 0) {
                $hex = '0' . $hex;
            }
            $output .= $hex;
        }
        return $output;
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
        $byteSize = $this->getByteSize();
        $hex      = $this->hexize($data);
        if ($byteSize > 0) {
            $hex = substr(str_pad($hex, $byteSize * 2, '0', STR_PAD_RIGHT), 0, $byteSize * 2);
        }

        $length = intval(strlen($hex) / 2);
        $output = '';
        if ($this->isDynamic()) {
            $output .= (new TypeUint())->serialize($length);
        }
        $paddingBytes = (int)ceil($length / static::PADDING_BYTES) * static::PADDING_BYTES;
        $output .= str_pad($hex, $paddingBytes * 2, '0', STR_PAD_RIGHT);

        return $output;
    }

    /**
     * @param string $data
     * @return string
     */
    public function deserialize(string $data)
    {
        if ($this->isDynamic()) {
            $length = (new TypeUint())->deserialize(substr($data, 0, 64));
            $data   = substr($data, 64, $length * 2);
        } else {
            $data = substr($data, 0, $this->getByteSize() * 2);
        }
        return $this->dehexize($data);
    }
}
