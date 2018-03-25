<?php

namespace Ethereum\Abi\Types;

class TypeByte extends TypeBytes
{
    public function __construct()
    {
        parent::__construct(1);
    }
}
