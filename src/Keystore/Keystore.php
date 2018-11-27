<?php

namespace Ethereum\Keystore;

use Ethereum\Collection;
use Ethereum\StorageInterface;
use Ethereum\Types\Byte;
use Exception;

class Keystore extends Collection
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     * @throws Exception
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $privateKey
     * @return $this
     * @throws Exception
     */
    public function addPrivateKey(string $privateKey)
    {
        $this->data[] = Key::initWithPrivateKey(Byte::initWithHex($privateKey));
        return $this;
    }

    /**
     * @param array $privateKeys
     * @return $this
     * @throws Exception
     */
    public function addPrivateKeys(array $privateKeys)
    {
        foreach ($privateKeys as $privateKey) {
            $this->addPrivateKey($privateKey);
        }
        return $this;
    }

    /**
     * @param string $keystore
     * @param string $passphrase
     * @return $this
     * @throws Exception
     */
    public function addKeystore(string $keystore, string $passphrase)
    {
        $this->data[] = Key::initWithKeystore($keystore, $passphrase);
        return $this;
    }

    /**
     * @param array $keystores
     * @return $this
     * @throws Exception
     */
    public function addKeystores(array $keystores)
    {
        foreach ($keystores as [$keystore, $passphrase]) {
            $this->addKeystore($keystore, $passphrase);
        }
        return $this;
    }

    /**
     * @return Key
     * @throws Exception
     */
    public function getNextKey(): Key
    {
        $count = count($this->data);
        if (! $count) {
            throw new Exception('No account exists.');
        }
        $num = $this->storage->increment('keystore_seq_num');
        $i = $num % $count;
        return $this->data[$i];
    }
}