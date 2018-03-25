<?php

namespace Ethereum\Types;

class TransactionReceipt
{
    /**
     * @var Hash
     */
    public $transactionHash;

    /**
     * @var Uint
     */
    public $transactionIndex;

    /**
     * @var Hash
     */
    public $blockHash;

    /**
     * @var BlockNumber
     */
    public $blockNumber;

    /**
     * @var Uint
     */
    public $cumulativeGasUsed;

    /**
     * @var Uint
     */
    public $gasUsed;

    /**
     * @var Address
     */
    public $contractAddress;

    /**
     * @var LogCollection
     */
    public $logs;

    /**
     * @var Byte
     */
    public $logsBloom;

    /**
     * @var Hash|null
     */
    public $root;

    /**
     * @var Uint|null
     */
    public $status;

    /**
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->transactionHash   = Hash::init($data['transactionHash']);
        $this->transactionIndex  = Uint::initWithHex($data['transactionIndex']);
        $this->blockHash         = Hash::init($data['blockHash']);
        $this->blockNumber       = BlockNumber::initWithHex($data['blockNumber']);
        $this->cumulativeGasUsed = Uint::initWithHex($data['cumulativeGasUsed']);
        $this->gasUsed           = Uint::initWithHex($data['gasUsed']);
        $this->contractAddress   = Address::init($data['contractAddress']);
        $this->logs              = new LogCollection($data['logs']);
        $this->logsBloom         = Byte::initWithHex($data['logsBloom']);

        if (! empty($data['root'])) {
            $this->root = Hash::init($data['root']);
        }

        if (! empty($data['status'])) {
            $this->status = Uint::initWithHex($data['status']);
        }
    }
}
