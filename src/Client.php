<?php

namespace Ethereum;

use Ethereum\Methods\Eth;
use Ethereum\Methods\Net;
use Ethereum\Methods\Web3;
use Ethereum\Types\Uint;
use Graze\GuzzleHttp\JsonRpc\Client as JsonRpcClient;
use Exception;

/**
 *
 * @property SmartContractCollection $contracts
 * @property Uint $chainId
 * @property Synchronizer $synchronizer
 * @property StorageInterface $storage
 * @property Uint $gasPrice
 * @property Uint $gasLimit
 */
class Client
{
    /**
     * @var JsonRpcClient
     */
    private $rpcClient;

    /**
     * @var Uint
     */
    private $chainId;

    /**
     * @var array
     */
    private $methods;

    /**
     * @var Keystore
     */
    private $keystore;

    /**
     * @var SmartContractCollection
     */
    private $contracts;

    /**
     * @var Synchronizer
     */
    private $synchronizer;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var Uint
     */
    private $gasPrice;

    /**
     * @var Uint
     */
    private $gasLimit;

    /**
     * @param string $url
     * @param int $chainId
     * @param string $keystore
     * @param string $passphrase
     * @param StorageInterface|null $storage
     */
    public function __construct(string $url, int $chainId, string $keystore, string $passphrase, ?StorageInterface $storage = null)
    {
        $this->rpcClient = JsonRpcClient::factory($url);
        $this->chainId   = Uint::init($chainId);
        $this->methods   = [
            'net'  => new Net($this->rpcClient),
            'eth'  => new Eth($this->rpcClient),
            'web3' => new Web3($this->rpcClient),
        ];
        $this->keystore     = new Keystore($keystore, $passphrase);
        $this->contracts    = new SmartContractCollection($this);
        $this->synchronizer = new Synchronizer($this, $this->contracts);
        $this->storage      = empty($storage) ? new Storage : $storage;
    }

    /**
     * @return Net
     */
    public function net(): Net
    {
        return $this->methods['net'];
    }

    /**
     * @return Web3
     */
    public function web3(): Web3
    {
        return $this->methods['web3'];
    }

    /**
     * @return Eth
     */
    public function eth(): Eth
    {
        return $this->methods['eth'];
    }

    /**
     * @return Keystore
     */
    public function keystore(): Keystore
    {
        return $this->keystore;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setGasPrice(int $value)
    {
        $this->gasPrice = empty($value) ? null : Uint::init($value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setGasLimit(int $value)
    {
        $this->gasLimit = empty($value) ? null : Uint::init($value);
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'contracts':
            case 'chainId':
            case 'synchronizer':
            case 'storage':
            case 'gasPrice':
            case 'gasLimit':
                return $this->{$name};
        }
        throw new Exception('Call to undefined property: ' . $name);
    }
}
