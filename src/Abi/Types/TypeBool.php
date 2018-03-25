<?php

namespace Ethereum\Abi\Types;

use Ethereum\Abi\Types\Abstracts\AbstractType;
use Ethereum\Abi\Types\Interfaces\TypeBoolInterface;

class TypeBool extends AbstractType implements TypeBoolInterface
{
    /**
     * @param string|int $data
     * @return string
     */
    public function hexize($data): string
    {
        return (new TypeUint())->hexize((int)$data);
    }

    /**
     * @param string $data
     * @return string
     */
    public function dehexize(string $data)
    {
        return (bool)(new TypeUint())->dehexize($data);
    }

    /**
     * @param bool $data
     * @return string
     */
    public function serialize($data): string
    {
        return $this->hexize($data);
    }

    /**
     * @param string $data
     * @return string
     */
    public function deserialize(string $data)
    {
        return $this->dehexize($data);
    }

    /**
     * @return bool
     */
    public function isDynamic(): bool
    {
        return false;
    }
}
