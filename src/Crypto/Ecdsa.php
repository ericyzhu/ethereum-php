<?php

namespace Ethereum\Crypto;

use Ethereum\Types\Address;
use Ethereum\Types\Byte;
use Ethereum\Types\Uint;
use Exception;

class Ecdsa
{
    /**
     * @param Byte $message
     * @param Byte $privateKey
     * @param bool $messageNeedHash
     * @return Byte
     * @throws Exception
     */
    public static function sign(Byte $message, Byte $privateKey, bool $messageNeedHash = false): Byte
    {
        if (strlen($privateKey->getHex()) != 64) {
            throw new Exception('Incorrect private key.');
        }

        if ($messageNeedHash) {
            $message = Byte::init(Keccak::hash($message->getBinary(), 256, true));
        }

        /** @var resource $ecdsaContext */
        $ecdsaContext = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        /** @var resource $ecdsaSignature */
        $ecdsaSignature = '';
        /** @var string $serialized */
        $serialized = '';
        /** @var int $recoveryId */
        $recoveryId = 0;
        try {
            if (secp256k1_ecdsa_sign_recoverable($ecdsaContext, $ecdsaSignature, $message->getBinary(), $privateKey->getBinary()) !== 1) {
                throw new Exception('Failed to create signature.');
            }
            if (secp256k1_ecdsa_recoverable_signature_serialize_compact($ecdsaContext, $ecdsaSignature, $serialized, $recoveryId) !== 1) {
                throw new Exception('Failed to serialize signature.');
            }
        } finally {
            unset($ecdsaContext, $ecdsaSignature);
        }
        return Byte::init($serialized.Uint::init($recoveryId)->getBinary());
    }

    /**
     * @param Byte $signature
     * @param Byte $message
     * @return Byte
     * @throws Exception
     */
    public static function recoverPublicKey(Byte $signature, Byte $message): Byte
    {
        /** @var resource $ecdsaContext */
        $ecdsaContext = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        /** @var resource $ecdsaSignature */
        $ecdsaSignature = '';
        secp256k1_ecdsa_recoverable_signature_parse_compact($ecdsaContext, $ecdsaSignature, $signature->slice(0, 64)->getBinary(), $signature->slice(64)->getInt());
        /** @var resource $ecdsaPublicKey */
        $ecdsaPublicKey = '';
        /** @var string $serialized */
        $serialized = '';
        try {
            if (secp256k1_ecdsa_recover($ecdsaContext, $ecdsaPublicKey, $ecdsaSignature, $message->getBinary()) !== 1) {
                throw new Exception('Failed to recover public key.');
            }
            if (secp256k1_ec_pubkey_serialize($ecdsaContext, $serialized, $ecdsaPublicKey, false) !== 1) {
                throw new Exception('Failed to serialize public key.');
            }
            $serialized = substr($serialized, 1, 64);
        } finally {
            unset($ecdsaContext, $ecdsaSignature, $ecdsaPublicKey);
        }
        return Byte::init($serialized);
    }

    /**
     * @param Byte $privateKey
     * @return Byte
     * @throws Exception
     */
    public static function createPublicKey(Byte $privateKey): Byte
    {
        /** @var resource $ecdsaContext */
        $ecdsaContext = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        /** @var resource $ecdsaPublicKey */
        $ecdsaPublicKey = '';
        /** @var string $serialized */
        $serialized = '';
        try {
            if (secp256k1_ec_pubkey_create($ecdsaContext, $ecdsaPublicKey, $privateKey->getBinary()) !== 1) {
                throw new Exception('Failed to create public key.');
            }
            if (secp256k1_ec_pubkey_serialize($ecdsaContext, $serialized, $ecdsaPublicKey, false) !== 1) {
                throw new Exception('Failed to serialize public key.');
            }
            $serialized = substr($serialized, 1, 64);
        } finally {
            unset($ecdsaPublicKey, $ecdsaContext);
        }
        return Byte::init($serialized);
    }

    /**
     * @param Byte $publicKey
     * @return Address
     * @throws Exception
     */
    public static function createAddress(Byte $publicKey): Address
    {
        $hash = Byte::init(Keccak::hash($publicKey->getBinary(), 256, true));
        return Address::initWithBuffer($hash->slice(12)->getBuffer());
    }
}