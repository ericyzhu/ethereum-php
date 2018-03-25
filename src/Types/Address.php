<?php

namespace Ethereum\Types;

use Ethereum\Utils;
use LengthException;
use Exception;

class Address extends TypeAbstract
{
    /**
     * @param string $address
     * @return Address
     * @throws Exception
     */
    public static function init($address = ''): Address
    {
        if (strlen($address) === 0) {
            $buffer = new Buffer;
        } else {
            $address = Utils::removeHexPrefix($address);
            if (strlen($address) !== 40) {
                throw new LengthException($address.' is invalid.');
            }
            $buffer = Buffer::hex($address);
        }

        return new static($buffer);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return Utils::ensureHexPrefix($this->getHex());
    }
}
