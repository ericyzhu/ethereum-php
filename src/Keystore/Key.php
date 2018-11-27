<?php

namespace Ethereum\Keystore;

use Ethereum\Crypto\Ecdsa;
use Ethereum\Crypto\Keccak;
use Ethereum\Crypto\TransactionSigner;
use Ethereum\Types\Address;
use Ethereum\Types\Byte;
use Ethereum\Types\Transaction;
use Ethereum\Types\Uint;
use Exception;
use InvalidArgumentException;

/**
 * Class Key
 * @package Ethereum\Keystore
 *
 * @property Byte $privateKey
 * @property Byte $publicKey
 * @property Address $address
 */
class Key
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
     * @param Byte $privateKey
     * @throws Exception
     */
    public function __construct(Byte $privateKey)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = Ecdsa::createPublicKey($this->privateKey);
        $this->address = Ecdsa::createAddress($this->publicKey);
    }

    /**
     * @param Byte $privateKey
     * @return Key
     * @throws Exception
     */
    public static function initWithPrivateKey(Byte $privateKey): Key
    {
        return new static($privateKey);
    }

    /**
     * @param string $keystore
     * @param string $passphrase
     * @return Key
     * @throws Exception
     */
    public static function initWithKeystore(string $keystore, string $passphrase): Key
    {
        try {
            $data = json_decode($keystore)->crypto;
        } catch (Exception $e) {
            throw new InvalidArgumentException('Argument is not a valid JSON string.');
        }

        switch ($data->kdf) {
            case 'pbkdf2':
                $derivedKey = static::derivePbkdf2EncryptedKey(
                    $passphrase,
                    $data->kdfparams->prf,
                    $data->kdfparams->salt,
                    $data->kdfparams->c,
                    $data->kdfparams->dklen
                );
                break;
            case 'scrypt':
                $derivedKey = static::deriveScryptEncryptedKey(
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

        if (! static::validateDerivedKey($derivedKey, $data->ciphertext, $data->mac)) {
            throw new Exception('Passphrase is invalid.');
        }
        $privateKey = static::decryptPrivateKey($data->ciphertext, $derivedKey, $data->cipher, $data->cipherparams->iv);
        return static::initWithPrivateKey($privateKey);
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
    private static function derivePbkdf2EncryptedKey(string $passphrase, string $prf, string $salt, int $c, $dklen)
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
    private static function deriveScryptEncryptedKey(string $passphrase, string $salt, int $n, int $r, int $p, int $dklen)
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
    private static function validateDerivedKey(string $key, string $ciphertext, string $mac)
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
    private static function decryptPrivateKey(string $ciphertext, string $key, string $cipher, string $iv): Byte
    {
        $output = openssl_decrypt(pack('H*', $ciphertext), $cipher, pack('H*', substr($key, 0, 32)),OPENSSL_RAW_DATA, pack('H*', $iv));
        return Byte::init($output);
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
        return $this->transactionSigner->sign($transaction, $this->privateKey);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->{$name};
    }
}