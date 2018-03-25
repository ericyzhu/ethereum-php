<?php

namespace Ethereum\Types;

use Ethereum\Collection;

class AddressCollection extends Collection
{
    /**
     * @param Address[] $addresses
     */
    public function __construct(array $addresses = [])
    {
        foreach ($addresses as $address) {
            if ($address instanceof Address) {
                $this->data[] = $address;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function (Address $address) {
            return $address->toString();
        }, $this->data);
    }
}