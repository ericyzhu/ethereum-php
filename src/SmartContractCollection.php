<?php

namespace Ethereum;

use Ethereum\Types\AddressCollection;
use UnexpectedValueException;

class SmartContractCollection extends Collection
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $name
     * @param string $address
     * @param string $abi
     * @return $this
     */
    public function add(string $name, string $address, string $abi)
    {
        $smartContract = new SmartContract($this->client, $address, $abi);
        $this->offsetSet($name, $smartContract);
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function remove(string $name)
    {
        unset($this->data[$name]);
        return $this;
    }

    public function getAddresses()
    {
        $addresses = new AddressCollection;
        /** @var SmartContract $smartContract */
        foreach ($this->data as $smartContract) {
            $addresses[] = $smartContract->getAddress();
        }
        return $addresses;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (empty($offset)) {
            throw new UnexpectedValueException('Name must be a string.');
        }

        if (empty($value) or ! $value instanceof SmartContract) {
            throw new UnexpectedValueException('Value must be an instance of "\Ethereum\SmartContract".');
        }

        $this->data[$offset] = $value;
        $this->alias[$value->getAddress()->toString()] = $offset;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return Utils::hasHexPrefix($offset) ? isset($this->alias[$offset]) : isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->alias[$this->data[$offset]->getAddtess()->toString()]);
            unset($this->data[$offset]);
        }
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        if (Utils::hasHexPrefix($offset)) {
            return isset($this->alias[$offset]) ? $this->data[$this->alias[$offset]] : null;
        }
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}