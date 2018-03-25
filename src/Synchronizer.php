<?php

namespace Ethereum;


use Ethereum\Types\BlockNumber;
use Ethereum\Types\Filter;
use Ethereum\Types\Log;
use Ethereum\Types\Uint;
use Throwable;

class Synchronizer
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var SmartContractCollection
     */
    private $contracts;

    /**
     * @var BlockNumber
     */
    private $startBlockNumber;

    /**
     * @var Uint
     */
    private $filterId;

    /**
     * @param Client $client
     * @param SmartContractCollection $contracts
     */
    public function __construct(Client $client, SmartContractCollection $contracts)
    {
        $this->client    = $client;
        $this->contracts = $contracts;
    }

    /**
     * @param BlockNumber $blockNumber
     * @return $this
     */
    public function setStartBlockNumber(BlockNumber $blockNumber): Synchronizer
    {
        $this->startBlockNumber = $blockNumber;
        return $this;
    }

    /**
     * @return BlockNumber
     */
    public function getLatestSyncedBlockNumber(): BlockNumber
    {
        $latestSyncedBlockNumber = $this->client->storage->get('latest_synced_block_number');
        if (empty($latestSyncedBlockNumber)) {
            $latestSyncedBlockNumber = empty($this->startBlockNumber) ? BlockNumber::init(BlockNumber::LATEST) : $this->startBlockNumber;
        } else {
            $latestSyncedBlockNumber = BlockNumber::init(Uint::init($latestSyncedBlockNumber));
        }
        return $latestSyncedBlockNumber;
    }

    /**
     * @param BlockNumber $blockNumber
     * @return $this
     */
    public function setLatestSyncedBlockNumber(BlockNumber $blockNumber): Synchronizer
    {
        $this->client->storage->set('latest_synced_block_number', $blockNumber->toString());
        return $this;
    }

    /**
     * @return Uint|null
     */
    public function getFilterId(): ?Uint
    {
        $filterId = $this->client->storage->get('synchronizer_filter_id');
        return empty($filterId) ? null : Uint::init((string)$filterId);
    }

    /**
     * @param Uint $filterId
     * @return $this
     */
    public function setFilterId(Uint $filterId): Synchronizer
    {
        $this->client->storage->set('synchronizer_filter_id', $filterId->toString());
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function registerFilter()
    {
        $filter = new Filter($this->getLatestSyncedBlockNumber(), BlockNumber::init(BlockNumber::LATEST), $this->contracts->getAddresses());
        $filterId = $this->client->eth()->newFilter($filter);
        $this->setFilterId($filterId);
    }

    /**
     * @throws \Exception
     */
    public function sync(): void
    {
        $filterId = $this->getFilterId();
        if (empty($filterId)) {
            $this->registerFilter();
        }
        try {
            $logs = $this->client->eth()->getFilterChanges($filterId);
        } catch (Throwable $e) {
            $this->registerFilter();
            return;
        }
        /** @var Log $log */
        foreach ($logs as $log) {
            $address = $log->address->toString();
            if (isset($this->contracts[$address])) {
                $this->contracts[$address]->dispatch($log);
            }
            $this->setLatestSyncedBlockNumber($log->blockNumber);
        }
    }
}