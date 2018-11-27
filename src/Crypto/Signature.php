<?php

namespace Ethereum\Crypto;

use Ethereum\Types\Address;
use Ethereum\Types\Byte;
use Exception;

class Signature
{
    /**
     * @param Byte $message
     * @param Byte $privateKey
     * @return Byte
     * @throws Exception
     */
    public static function sign(Byte $message, Byte $privateKey): Byte
    {
        return Ecdsa::sign($message, $privateKey, true);
    }

    /**
     * @param Byte $hash
     * @param Byte $signature
     * @return Byte
     * @throws Exception
     */
    public static function recoverPublicKey(Byte $hash, Byte $signature): Byte
    {
        return Ecdsa::recoverPublicKey($signature, $hash);
    }

    /**
     * @param Byte $publicKey
     * @return Address
     * @throws Exception
     */
    public static function publicKeyToAddress(Byte $publicKey): Address
    {
        return Ecdsa::createAddress($publicKey);
    }
}