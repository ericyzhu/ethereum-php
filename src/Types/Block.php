<?php

namespace Ethereum\Types;

class Block
{
    /**
     * @var Uint
     */
    public $difficulty;

    /**
     * @var Byte
     */
    public $extraData;

    /**
     * @var Uint
     */
    public $gasLimit;

    /**
     * @var Uint
     */
    public $gasUsed;

    /**
     * @var Hash
     */
    public $hash;

    /**
     * @var Byte
     */
    public $logsBloom;

    /**
     * @var Address
     */
    public $miner;

    /**
     * @var Hash
     */
    public $mixHash;

    /**
     * @var Uint
     */
    public $nonce;

    /**
     * @var Uint
     */
    public $number;

    /**
     * @var Hash
     */
    public $parentHash;

    /**
     * @var Hash
     */
    public $receiptsRoot;

    /**
     * @var Hash
     */
    public $sha3Uncles;

    /**
     * @var Uint
     */
    public $size;

    /**
     * @var Hash
     */
    public $stateRoot;

    /**
     * @var Uint
     */
    public $timestamp;

    /**
     * @var Uint
     */
    public $totalDifficulty;

    /**
     * @var Hash
     */
    public $transactionsRoot;

    /**
     * @var HashCollection
     */
    public $uncles;

    /**
     * @var array
     */
    public $transactions;

    /**
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->difficulty       = Uint::initWithHex($data['difficulty']);
        $this->extraData        = Byte::initWithHex($data['extraData']);
        $this->gasLimit         = Uint::initWithHex($data['gasLimit']);
        $this->gasUsed          = Uint::initWithHex($data['gasUsed']);
        $this->hash             = Hash::init($data['hash']);
        $this->logsBloom        = Byte::initWithHex($data['logsBloom']);
        $this->miner            = Address::init($data['miner']);
        $this->mixHash          = Hash::init($data['mixHash']);
        $this->nonce            = Uint::initWithHex($data['nonce']);
        $this->number           = Uint::initWithHex($data['number']);
        $this->parentHash       = Hash::init($data['parentHash']);
        $this->receiptsRoot     = Hash::init($data['receiptsRoot']);
        $this->sha3Uncles       = Hash::init($data['sha3Uncles']);
        $this->size             = Uint::initWithHex($data['size']);
        $this->stateRoot        = Hash::init($data['stateRoot']);
        $this->timestamp        = Uint::initWithHex($data['timestamp']);
        $this->totalDifficulty  = Uint::initWithHex($data['totalDifficulty']);
        $this->transactionsRoot = Hash::init($data['transactionsRoot']);
        $this->uncles           = new HashCollection($data['uncles']);
        $this->transactions     = [];
        foreach ($data['transactions'] as $transaction) {
            if (is_string($transaction)) {
                $this->transactions[] = Hash::init($transaction);
            } elseif (is_array($transaction)) {
                $this->transactions[] = new TransactionInfo($transaction);
            }
        }
    }
}
