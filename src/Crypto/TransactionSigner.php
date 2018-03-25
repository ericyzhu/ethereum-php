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

        /** @var int $recoveryId */
        $recoveryId = 0;
        $signature = Signature::sign($hash, $privateKey, $recoveryId);

        $r = Uint::initWithHex(Utils::trimHex(substr($signature->getHex(), 0, 64)));
        $s = Uint::initWithHex(Utils::trimHex(substr($signature->getHex(), 64)));
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