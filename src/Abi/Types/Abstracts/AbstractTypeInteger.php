<?php

namespace Ethereum\Abi\Types\Abstracts;

use Ethereum\Abi\Types\Interfaces\TypeIntegerInterface;
use InvalidArgumentException;

abstract class AbstractTypeInteger extends AbstractType implements TypeIntegerInterface
{
    /**
     * @var int
     */
    protected $bitSize;

    /**
     * @param int|null $bitSize
     * @throws \InvalidArgumentException
     */
    public function __construct($bitSize = 256)
    {
        if ($bitSize < 0 or $bitSize > 256 or $bitSize % 8 !== 0) {
            throw new InvalidArgumentException('The bit size is invalid.');
        }
        $this->bitSize = (int)$bitSize;
    }

    /**
     * @return int
     */
    public function getBitSize(): int
    {
        return $this->bitSize;
    }

    /**
     * @return bool
     */
    public function isDynamic(): bool
    {
        return false;
    }
}
