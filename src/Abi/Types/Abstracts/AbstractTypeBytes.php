<?php

namespace Ethereum\Abi\Types\Abstracts;

use Ethereum\Abi\Types\Interfaces\TypeBytesInterface;
use InvalidArgumentException;

abstract class AbstractTypeBytes extends AbstractType implements TypeBytesInterface
{
    /**
     * @var int
     */
    protected $byteSize;

    /**
     * @param int $byteSize
     */
    public function __construct($byteSize = null)
    {
        if ($byteSize !== null and ($byteSize < 0 or $byteSize > 32)) {
            throw new InvalidArgumentException('The byte size is invalid.');
        }
        $this->byteSize = (int)$byteSize;
    }

    /**
     * @return int
     */
    public function getByteSize(): int
    {
        return $this->byteSize;
    }

    /**
     * @return bool
     */
    public function isDynamic(): bool
    {
        return $this->getByteSize() === 0;
    }
}
