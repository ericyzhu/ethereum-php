<?php

namespace Ethereum\Types;


class Filter
{
    /**
     * @var BlockNumber|null
     */
    public $fromBlock;

    /**
     * @var BlockNumber|null
     */
    public $toBlock;

    /**
     * @var AddressCollection|null
     */
    public $address;

    /**
     * @var HashCollection|null
     */
    public $topics;

    /**
     * @param BlockNumber|null $fromBlock
     * @param BlockNumber|null $toBlock
     * @param AddressCollection|null $address
     * @param HashCollection|null $topics
     */
    public function __construct(?BlockNumber $fromBlock = null, ?BlockNumber $toBlock = null, ?AddressCollection $address = null, ?HashCollection $topics = null)
    {
        $this->fromBlock = $fromBlock;
        $this->toBlock   = $toBlock;
        $this->address   = $address;
        $this->address   = $topics;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];

        if (! $this->fromBlock->isEmpty()) {
            $array['fromBlock'] = $this->fromBlock->toString();
        }

        if (! $this->toBlock->isEmpty()) {
            $array['toBlock'] = $this->toBlock->toString();
        }

        if (! empty($this->address)) {
            $array['address'] = $this->address->toArray();
        }

        if (! empty($this->topics)) {
            $array['topics'] = $this->topics->toArray();
        }

        return $array;
    }
}