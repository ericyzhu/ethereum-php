<?php

namespace Ethereum\Crypto;

use Ethereum\Types\Address;
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
     * @param Byte $hash
     * @param Byte $signature
     * @return Byte
     * @throws Exception
     */
    public static function recoverPublicKey(Byte $hash, Byte $signature): Byte
    {
        /** @var resource $context */
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        /** @var resource $secpSignature */
        $secpSignature = '';
        $recoveryId = $signature->slice(64)->getInt();
        secp256k1_ecdsa_recoverable_signature_parse_compact($context, $secpSignature, $signature->slice(0, 64)->getBinary(), $recoveryId);
        /** @var resource $secpPublicKey */
        $secpPublicKey = '';
        secp256k1_ecdsa_recover($context, $secpPublicKey, $secpSignature, $hash->getBinary());
        $publicKey = '';
        secp256k1_ec_pubkey_serialize($context, $publicKey, $secpPublicKey, 0);
        unset($context, $secpSignature, $secpPublicKey);
        return Byte::init($publicKey);
    }

    /**
     * @param Byte $publicKey
     * @return Address
     * @throws Exception
     */
    public static function publicKeyToAddress(Byte $publicKey): Address
    {
        $ret = Byte::initWithHex(Keccak::hash($publicKey->slice(1)->getBinary()));
        return Address::initWithBuffer($ret->slice(12)->getBuffer());
    }
}