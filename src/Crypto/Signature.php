<?php

namespace Ethereum\Crypto;

use Ethereum\Types\Byte;
use Exception;

class Signature
{
    /**
     * @param Byte $hash
     * @param Byte $privateKey
     * @param int $recoveryId
     * @return Byte
     * @throws Exception
     */
    public static function sign(Byte $hash, Byte $privateKey, &$recoveryId): Byte
    {
        /** @var resource $context */
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        if (strlen($privateKey->getHex()) != 64) {
            throw new Exception("Incorrect private key");
        }
        /** @var resource $signature */
        $signature = '';
        if (secp256k1_ecdsa_sign_recoverable($context, $signature, $hash->getBinary(), $privateKey->getBinary()) != 1) {
            throw new Exception("Failed to create signature");
        }
        /** @var string $serialized */
        $serialized = '';
        secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $signature, $serialized, $recoveryId);

        unset($context, $signature);

        return Byte::init($serialized);
    }

    /**
     * @todo
     */
    public static function ecrecover()
    {
    }
}