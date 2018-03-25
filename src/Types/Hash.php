<?php

namespace Ethereum\Types;

use Ethereum\Utils;
use LengthException;

class Hash extends TypeAbstract
{
    /**
     * @param string $hash
     * @return Hash
     * @throws \Exception
     */
    public static function init(string $hash): Hash
    {
        $hash = Utils::removeHexPrefix($hash);
        if (strlen($hash) !== 64) {
            throw new LengthException($hash.' is not valid.');
        }
        return new static(Buffer::hex($hash));
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return Utils::ensureHexPrefix($this->getHex());
    }
}
