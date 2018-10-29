<?php

namespace Ethereum\Crypto;

use Ethereum\Rlp;
use Ethereum\Types\Byte;
use Ethereum\Types\Transaction;
use Ethereum\Types\Uint;
use Ethereum\Utils;
use Exception;

final class TransactionSigner
{
    /**
     * @var Uint
     */
    protected $chainId;

    /**
     * @var Uint
     */
    protected $chainIdMul;

    /**
     * @param Uint $chainId
     */
    public function __construct(Uint $chainId)
    {
        $this->chainId    = $chainId;
        $this->chainIdMul = Uint::init($chainId->getInt() * 2);
    }

    /**
     * @param Transaction $transaction
     * @param Byte $privateKey
     * @return Byte
     * @throws Exception
     */
    public function sign(Transaction $transaction, Byte $privateKey): Byte
    {
        /** @var Byte $hash */
        $hash = $this->hash($transaction);

        $signature = Signature::sign($hash, $privateKey);

        //$r = Uint::initWithBuffer($signature->slice(0, 32)->getBuffer());
        //$s = Uint::initWithBuffer($signature->slice(32, 32)->getBuffer());
        if ($signature->slice(0,1)->getBinary() === Byte::initWithHex('00')->getBinary()) {
            $r = Uint::initWithBuffer($signature->slice(1, 31)->getBuffer());
        }else{
            $r = Uint::initWithBuffer($signature->slice(0, 32)->getBuffer());
        }
        
        if ($signature->slice(32,1)->getBinary() === Byte::initWithHex('00')->getBinary()) {
            $s = Uint::initWithBuffer($signature->slice(33, 31)->getBuffer());
        }else{
            $s = Uint::initWithBuffer($signature->slice(32, 32)->getBuffer());
        }
        
        $recoveryId = $signature->slice(64)->getInt();
        if ($this->chainId->getInt() > 0) {
            $v = Uint::init($recoveryId + 35 + $this->chainIdMul->getInt());
        } else {
            $v = Uint::init($recoveryId + 27);
        }

        return $transaction->withSignature($v, $r, $s);
    }

    /**
     * @param Transaction $transaction
     * @return Byte
     * @throws Exception
     */
    protected function hash(Transaction $transaction): Byte
    {
        $raw = [
            $transaction->nonce,
            $transaction->gasPrice,
            $transaction->gas,
            $transaction->to,
            $transaction->value,
            $transaction->data,
        ];

        if ($this->chainId->getInt() > 0) {
            $raw = array_merge($raw, [
                $this->chainId,
                Uint::init(),
                Uint::init(),
            ]);
        }

        $hash = Rlp::encode($raw);
        return Byte::init(Keccak::hash($hash->getBinary(), 256, true));
    }
}
