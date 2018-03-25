<?php

namespace Ethereum\Types;

use Ethereum\Abi\Structs\StructEvent;
use Exception;

class Event
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
     * @var array
     */
    public $data;

    /**
     * @var StructEvent
     */
    public $struct;

    /**
     * @param StructEvent $struct
     * @param Log $log
     * @param array $data
     * @throws Exception
     */
    public function __construct(StructEvent $struct, Log $log, array $data)
    {
        $this->struct           = $struct;
        $this->removed          = $log->removed;
        $this->logIndex         = $log->logIndex;
        $this->transactionIndex = $log->transactionIndex;
        $this->transactionHash  = $log->transactionHash;
        $this->blockHash        = $log->blockHash;
        $this->blockNumber      = $log->blockNumber;
        $this->address          = $log->address;
        $this->data             = $data;
    }
}