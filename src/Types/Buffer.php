<?php

namespace Ethereum\Types;

use BitWasp\Buffertools\BufferInterface;

class Buffer extends \BitWasp\Buffertools\Buffer
{
    /**
     * @param string $byteString
     * @param int|null $byteSize
     * @throws \Exception
     */
    public function __construct(string $byteString = '', ?int $byteSize = null)
    {
        parent::__construct($byteString, $byteSize);
    }

    /**
     * @param string $hexString
     * @param int|null $byteSize
     * @return Buffer
     * @throws \Exception
     */
    public static function hex(string $hexString = '', int $byteSize = null): BufferInterface
    {
        if (strlen($hexString) > 0 && !ctype_xdigit($hexString)) {
            throw new \InvalidArgumentException('Buffer::hex: non-hex character passed');
        }

        $binary = pack("H*", $hexString);
        return new static($binary, $byteSize);
    }

    /**
     * @param int|string $integer
     * @param null|int $byteSize
     * @return Buffer
     */
    public static function int($integer, $byteSize = null): BufferInterface
    {
        if ($integer < 0) {
            throw new \InvalidArgumentException('Negative integers not supported by Buffer::int. This could be an application error, or you should be using templates.');
        }

        $hex = gmp_strval(gmp_init($integer, 10), 16);
        if ((mb_strlen($hex) % 2) !== 0) {
            $hex = "0{$hex}";
        }

        $binary = pack("H*", $hex);
        return new static($binary, $byteSize);
    }

    /**
     * @param int      $start
     * @param integer|null $end
     * @return BufferInterface
     * @throws \Exception
     */
    public function slice(int $start, int $end = null): BufferInterface
    {
        if ($start > $this->getSize()) {
            throw new \Exception('Start exceeds buffer length');
        }

        if ($end === null) {
            return new static(substr($this->getBinary(), $start));
        }

        if ($end > $this->getSize()) {
            throw new \Exception('Length exceeds buffer length');
        }

        $string = substr($this->getBinary(), $start, $end);
        if (!is_string($string)) {
            throw new \RuntimeException('Failed to slice string of with requested start/end');
        }

        $length = strlen($string);
        return new static($string, $length);
    }
}