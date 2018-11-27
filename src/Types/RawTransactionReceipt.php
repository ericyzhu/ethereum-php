<?php

namespace Ethereum\Types;

class RawTransactionReceipt
{
    /**
     * @var Hash
     */
    public $transactionHash;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @param Hash $transactionHash
     * @param Transaction $transaction
     * @throws \Exception
     */
    public function __construct(Hash $transactionHash, Transaction $transaction)
    {
        $this->transactionHash = $transactionHash;
        $this->transaction     = $transaction;
    }
}
