<?php

declare(strict_types=1);

namespace Ethereum\Abi\Types;

use Ethereum\Abi\Types\Abstracts\AbstractType;
use Ethereum\Abi\Types\Interfaces\TypeAddressInterface;
use Ethereum\Utils;

class TypeAddress extends AbstractType implements TypeAddressInterface
{
    /**
     * @param string|int $data
     * @return string
     */
    public function hexize($data): string
    {
        $uint = new TypeUint(160);
        return $uint->hexize($uint->dehexize($data));
    }

    /**
     * @param string $data
     * @return string
     */
    public function dehexize(string $data)
    {
        $unit = new TypeUint(160);
        return str_pad(substr($unit->hexize($unit->dehexize($data)), -40, 40), 40, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $data
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
        return Utils::ensureHexPrefix($this->dehexize($data));
    }

    /**
     * @return bool
     */
    public function isDynamic(): bool
    {
        return false;
    }
}
