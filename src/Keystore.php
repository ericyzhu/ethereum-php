<?php

namespace Ethereum;

use Ethereum\Crypto\Keccak;
use Ethereum\Crypto\TransactionSigner;
use Ethereum\Types\Address;
use Ethereum\Types\Byte;
use Ethereum\Types\Transaction;
use Ethereum\Types\Uint;
use Exception;
use InvalidArgumentException;

class Keystore
{
    /**
     * @var Byte
     */
    private $privateKey;

    /**
     * @var Byte
     */
    private $publicKey;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var TransactionSigner
     */
    private $transactionSigner;

    /**
     * @param string $data
     * @param string $passphrase
     * @throws Exception
     */
    public function __construct(string $data, string $passphrase)
    {
        try {
            $data = json_decode($data)->crypto;
        } catch (Exception $e) {
            throw new InvalidArgumentException('Argument is not a valid JSON string.');
        }

        switch ($data->kdf) {
            case 'pbkdf2':
                $derivedKey = $this->derivePbkdf2EncryptedKey(
                    $passphrase,
                    $data->kdfparams->prf,
                    $data->kdfparams->salt,
                    $data->kdfparams->c,
                    $data->kdfparams->dklen
                );
                break;
            case 'scrypt':
                $derivedKey = $this->deriveScryptEncryptedKey(
                    $passphrase,
                    $data->kdfparams->salt,
                    $data->kdfparams->n,
                    $data->kdfparams->r,
                    $data->kdfparams->p,
                    $data->kdfparams->dklen
                );
                break;
            default:
                throw new Exception(sprintf('Unsupported KDF function "%s".', $data->kdf));
        }

        if (! $this->validateDerivedKey($derivedKey, $data->ciphertext, $data->mac)) {
            throw new Exception('Passphrase is invalid.');
        }

        $this->privateKey = $this->decryptPrivateKey($data->ciphertext, $derivedKey, $data->cipher, $data->cipherparams->iv);
        $this->publicKey = $this->createPublicKey($this->privateKey);
        $this->address = $this->parseAddress($this->publicKey);
    }

    /**
     * @param string $passphrase
     * @param string $prf
     * @param string $salt
     * @param int $c
     * @param $dklen
     * @return string
     * @throws Exception
     */
    private function derivePbkdf2EncryptedKey(string $passphrase, string $prf, string $salt, int $c, $dklen)
    {
        if ($prf != 'hmac-sha256') {
            throw new Exception(sprintf('Unsupported PRF function "%s".', $prf));
        }
        return hash_pbkdf2('sha256', $passphrase, pack('H*', $salt), $c,  $dklen * 2);
    }

    /**
     * @param string $passphrase
     * @param string $salt
     * @param int $n
     * @param int $r
     * @param int $p
     * @param int $dklen
     * @return string
     */
    private function deriveScryptEncryptedKey(string $passphrase, string $salt, int $n, int $r, int $p, int $dklen)
    {
        return scrypt($passphrase, pack('H*', $salt), $n, $r, $p, $dklen);
    }

    /**
     * @param string $key
     * @param string $ciphertext
     * @param string $mac
     * @return bool
     * @throws Exception
     */
    private function validateDerivedKey(string $key, string $ciphertext, string $mac)
    {
        return Keccak::hash(pack('H*', substr($key, 32, 32).$ciphertext)) === $mac;
    }

    /**
     * @param string $ciphertext
     * @param string $key
     * @param string $cipher
     * @param string $iv
     * @return Byte
     * @throws Exception
     */
    private function decryptPrivateKey(string $ciphertext, string $key, string $cipher, string $iv): Byte
    {
        $output = openssl_decrypt(pack('H*', $ciphertext), $cipher, pack('H*', substr($key, 0, 32)),OPENSSL_RAW_DATA, pack('H*', $iv));
        return Byte::init($output);
    }

    /**
     * @param Byte $privateKey
     * @return Byte
     * @throws Exception
     */
    private function createPublicKey(Byte $privateKey): Byte
    {
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        /** @var resource $publicKey */
        $publicKey = '';
        $result = secp256k1_ec_pubkey_create($context, $publicKey, $privateKey->getBinary());
        if ($result === 1) {
            $serialized = '';
            if (1 !== secp256k1_ec_pubkey_serialize($context, $serialized, $publicKey, false)) {
                throw new Exception('secp256k1_ec_pubkey_serialize: failed to serialize public key');
            }
            $serialized = substr($serialized, 1, 64);
            unset($publicKey, $context);
            return Byte::init($serialized);
        }
        throw new Exception('secp256k1_pubkey_create: secret key was invalid');
    }

    /**
     * @param Byte $publicKey
     * @return Address
     * @throws Exception
     */
    private function parseAddress(Byte $publicKey): Address
    {
        $hash = Keccak::hash($publicKey->getBinary());
        return Address::init(substr($hash, -40, 40));
    }

    /**
     * @return Byte
     */
    public function getPrivateKey(): Byte
    {
        return $this->privateKey;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Transaction $transaction
     * @param Uint $chainId
     * @return Byte
     * @throws Exception
     */
    public function signTransaction(Transaction $transaction, Uint $chainId): Byte
    {
        if (empty($this->transactionSigner)) {
            $this->transactionSigner = new TransactionSigner($chainId);
        }
        return $this->transactionSigner->sign($transaction, $this->getPrivateKey());
    }
}