<?php

namespace Ethereum\Types;

use Exception;

class Log
{
    /**
     * @var bool
     */
    public $removed;

    /**
     * @var Uint
     */
    public $logIndex;

    /**
     * @var Uint
     */
    public $transactionIndex;

    /**
     * @var static
     */
    public $transactionHash;

    /**
     * @var static
     */
    public $blockHash;

    /**
     * @var BlockNumber
     */
    public $blockNumber;

    /**
     * @var Address
     */
    public $address;

    /**
     * @var string
     */
    public $data;

    /**
     * @var HashCollection
     */
    public $topics;

    /**
     * @param array $data
     * @throws Exception
     */
    public function __construct(array $data)
    {
        $this->removed          = $data['removed'];
        $this->logIndex         = Uint::initWithHex($data['logIndex']);
        $this->transactionIndex = Uint::initWithHex($data['transactionIndex']);
        $this->transactionHash  = Hash::init($data['transactionHash']);
        $this->blockHash        = Hash::init($data['blockHash']);
        $this->blockNumber      = BlockNumber::initWithHex($data['blockNumber']);
        $this->address          = Address::init($data['address']);
        $this->data             = $data['data'];
        $this->topics           = new HashCollection($data['topics']);
    }

    public function toArray()
    {
        return [

        ];
    }
}