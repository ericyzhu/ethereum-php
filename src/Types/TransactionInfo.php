<?php

namespace Ethereum\Types;

class TransactionInfo
{
    /**
     * @var Hash
     */
    public $blockHash;

    /**
     * @var BlockNumber
     */
    public $blockNumber;

    /**
     * @var Address
     */
    public $from;

    /**
     * @var Address|null
     */
    public $to;

    /**
     * @var Uint
     */
    public $gas;

    /**
     * @var Uint
     */
    public $gasPrice;

    /**
     * @var Hash
     */
    public $hash;

    /**
     * @var Uint
     */
    public $nonce;

    /**
     * @var Uint
     */
    public $transactionIndex;

    /**
     * @var Uint
     */
    public $value;

    /**
     * @var Byte
     */
    public $input;

    //public $v;
    //public $r;
    //public $s;

    /**
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->blockHash        = Hash::init($data['blockHash']);
        $this->blockNumber      = BlockNumber::initWithHex($data['blockNumber']);
        $this->from             = Address::init($data['from']);
        $this->to               = empty($data['to']) ? null : Address::init($data['to']);
        $this->gas              = Uint::initWithHex($data['gas']);
        $this->gasPrice         = Uint::initWithHex($data['gasPrice']);
        $this->hash             = Hash::init($data['hash']);
        $this->nonce            = Uint::initWithHex($data['nonce']);
        $this->transactionIndex = Uint::initWithHex($data['transactionIndex']);
        $this->value            = Uint::initWithHex(hexdec($data['value']));
        $this->input            = Byte::initWithHex($data['input']);
        //$this->v = $data['v'];
        //$this->r = $data['r'];
        //$this->s = $data['s'];
    }
}
