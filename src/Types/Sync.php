<?php

namespace Ethereum\Types;

class Sync
{
    /**
     * @var Uint
     */
    public $startingBlock;

    /**
     * @var Uint
     */
    public $currentBlock;

    /**
     * @var Uint
     */
    public $highestBlock;

    /**
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->startingBlock = Uint::initWithHex($data['startingBlock']);
        $this->currentBlock  = Uint::initWithHex($data['currentBlock']);
        $this->highestBlock  = Uint::initWithHex($data['highestBlock']);
    }
}
