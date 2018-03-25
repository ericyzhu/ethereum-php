<?php

namespace Ethereum\Methods;

use Ethereum\Types\Address;
use Ethereum\Types\Block;
use Ethereum\Types\BlockNumber;
use Ethereum\Types\Byte;
use Ethereum\Types\Filter;
use Ethereum\Types\LogCollection;
use Ethereum\Types\Sync;
use Ethereum\Types\Transaction;
use Ethereum\Types\Hash;
use Ethereum\Types\TransactionInfo;
use Ethereum\Types\TransactionReceipt;
use Ethereum\Types\Uint;
use Ethereum\Utils;

class Eth extends AbstractMethods
{
    /**
     * Returns the current ethereum protocol version.
     *
     * @return Uint
     * The current ethereum protocol version
     *
     * @throws \Exception
     */
    public function protocolVersion(): Uint
    {
        $response = $this->_send($this->_request(67, __FUNCTION__, []));
        return Uint::initWithHex($response);
    }

    /**
     * Returns an object with data about the sync status or false.
     *
     * @return Sync|bool
     * Object|Boolean, An object with sync status data or FALSE, when not syncing:
     *  - startingBlock:
     *    The block at which the import started (will only be reset, after the sync reached his head)
     *  - currentBlock:
     *    The current block, same as eth_blockNumber
     *  - highestBlock:
     *    The estimated highest block
     */
    public function syncing()
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, []));
        if (is_bool($response)) {
            return (bool)$response;
        }
        return new Sync($response);
    }

    /**
     * Returns the client coinbase address.
     *
     * @return Address|null
     * 20 bytes - the current coinbase address.
     *
     * @throws \Exception
     */
    public function coinbase(): ?Address
    {
        $response = $this->_send($this->_request(64, __FUNCTION__, []));
        return empty($response) ? null : Address::init($response);
    }

    /**
     * Returns true if client is actively mining new blocks.
     *
     * @return bool
     * Returns true of the client is mining, otherwise false.
     */
    public function mining(): bool
    {
        $response = $this->_send($this->_request(71, __FUNCTION__, []));
        return (bool)$response;
    }

    /**
     * Returns the number of hashes per second that the node is mining with.
     *
     * @return Uint
     * Number of hashes per second.
     *
     * @throws \Exception
     */
    public function hashrate(): Uint
    {
        $response = $this->_send($this->_request(71, __FUNCTION__, []));
        return Uint::initWithHex($response);
    }

    /**
     * Returns the current price per gas in wei.
     *
     * @return Uint
     * Integer of the current gas price in wei.
     *
     * @throws \Exception
     */
    public function gasPrice(): Uint
    {
        $response = $this->_send($this->_request(73, __FUNCTION__, []));
        return Uint::initWithHex($response);
    }

    /**
     * Returns a list of addresses owned by client.
     *
     * @return Address[]
     * Array of Address, 20 Bytes - addresses owned by the client.
     *
     * @throws \Exception
     */
    public function accounts(): array
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, []));
        $addresses = [];
        foreach ($response as $address) {
            $addresses[] = Address::init($address);
        }
        return $addresses;
    }

    /**
     * Returns the number of most recent block.
     *
     * @return Uint
     * Iinteger of the current block number the client is on.
     *
     * @throws \Exception
     */
    public function blockNumber(): Uint
    {
        $response = $this->_send($this->_request(83, __FUNCTION__, []));
        return Uint::initWithHex($response);
    }

    /**
     * Returns the balance of the account of given address.
     *
     * @param Address $address
     * 20 Bytes - address to check for balance.
     *
     * @param BlockNumber $blockNumber
     * Integer block number, or the string "latest", "earliest" or "pending", @see https://github.com/ethereum/wiki/wiki/JSON-RPC#the-default-block-parameter
     *
     * @return Uint
     * Integer of the current balance in wei.
     *
     * @throws \Exception
     */
    public function getBalance(Address $address, BlockNumber $blockNumber): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$address->toString(), $blockNumber->toString()]));
        return Uint::initWithHex($response);
    }

    /**
     * Returns the value from a storage position at a given address.
     *
     * @param Address $address
     * Address of the storage.
     *
     * @param Uint $quantity
     * Integer of the position in the storage.
     *
     * @param BlockNumber $blockNumber
     * Integer block number, or the string "latest", "earliest" or "pending", see the default block parameter
     *
     * @return Uint
     * The value at this storage position.
     *
     * @throws \Exception
     *
     * @see https://github.com/ethereum/wiki/wiki/JSON-RPC#eth_getstorageat
     */
    public function getStorageAt(Address $address, Uint $quantity, BlockNumber $blockNumber): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$address->toString(), Utils::ensureHexPrefix($quantity->getHex()), $blockNumber->toString()]));
        return Uint::initWithHex($response);
    }

    /**
     * Returns the number of transactions sent from an address.
     *
     * @param Address $address
     * 20 Bytes - address to check for balance.
     *
     * @param BlockNumber $blockNumber
     * Integer block number, or the string "latest", "earliest" or "pending", @see https://github.com/ethereum/wiki/wiki/JSON-RPC#the-default-block-parameter
     *
     * @return Uint
     * Integer of the number of transactions send from this address.
     *
     * @throws \Exception
     */
    public function getTransactionCount(Address $address, BlockNumber $blockNumber): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$address->toString(), $blockNumber->toString()]));
        return Uint::initWithHex($response);
    }

    /**
     * Returns the number of transactions in a block from a block matching the given block hash.
     *
     * @param Hash $hash
     * 32 Bytes - hash of a block
     *
     * @return Uint
     * Integer of the number of transactions in this block.
     *
     * @throws \Exception
     */
    public function getBlockTransactionCountByHash(Hash $hash): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$hash->toString()]));
        return Uint::initWithHex($response);

    }

    /**
     * Returns the number of transactions in a block matching the given block number.
     *
     * @param BlockNumber $blockNumber
     * Integer block number, or the string "latest", "earliest" or "pending", @see https://github.com/ethereum/wiki/wiki/JSON-RPC#the-default-block-parameter
     *
     * @return Uint
     * Integer of the number of transactions in this block.
     *
     * @throws \Exception
     */
    public function getBlockTransactionCountByNumber(BlockNumber $blockNumber): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$blockNumber->toString()]));
        return Uint::initWithHex($response);

    }

    /**
     * Returns the number of uncles in a block from a block matching the given block hash.
     *
     * @param Hash $hash
     * 32 Bytes - hash of a block
     *
     * @return Uint
     * Integer of the number of uncles in this block.
     *
     * @throws \Exception
     */
    public function getUncleCountByBlockHash(Hash $hash): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$hash->toString()]));
        return Uint::initWithHex($response);

    }

    /**
     * Returns the number of uncles in a block from a block matching the given block number.
     *
     * @param BlockNumber $blockNumber
     * Integer block number, or the string "latest", "earliest" or "pending", @see https://github.com/ethereum/wiki/wiki/JSON-RPC#the-default-block-parameter
     *
     * @return Uint
     * Integer of the number of uncles in this block.
     *
     * @throws \Exception
     */
    public function getUncleCountByBlockNumber(BlockNumber $blockNumber): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$blockNumber->toString()]));
        return Uint::initWithHex($response);

    }

    /**
     * Returns code at a given address.
     *
     * @param Address $address
     * 20 Bytes - address
     *
     * @param BlockNumber $blockNumber
     * Integer block number, or the string "latest", "earliest" or "pending", see the default block parameter
     *
     * @return Byte
     * The code from the given address.
     *
     * @throws \Exception
     */
    public function getCode(Address $address, BlockNumber $blockNumber)
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$address->toString(), $blockNumber->toString()]));
        return Byte::initWithHex($response);
    }

    /**
     * The sign method calculates an Ethereum specific signature with:
     * sign(keccak256("\x19Ethereum Signed Message:\n" + len(message) + message))).
     *
     * By adding a prefix to the message makes the calculated signature recognisable as an Ethereum specific signature.
     * This prevents misuse where a malicious DApp can sign arbitrary data (e.g. transaction) and use the signature to impersonate the victim.
     *
     * Note the address to sign with must be unlocked.
     *
     * @param Address $address
     * 20 Bytes - address
     *
     * @param Byte $msgToSign
     * N Bytes - message to sign
     *
     * @return Byte
     * Signature
     *
     * @throws \Exception
     */
    public function sign(Address $address, Byte $msgToSign): Byte
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$address->toString(), Utils::ensureHexPrefix($msgToSign->getHex())]));
        return Byte::initWithHex($response);
    }

    /**
     * Creates new message call transaction or a contract creation, if the data field contains code.
     *
     * @param Transaction $transaction
     * The transaction object
     *  - from:
     *    20 Bytes - The address the transaction is send from.
     *  - to:
     *    20 Bytes - (optional when creating new contract) The address the transaction is directed to.
     *  - gas:
     *    (optional, default: 90000) Integer of the gas provided for the transaction execution. It will return unused gas.
     *  - gasPrice:
     *    (optional, default: To-Be-Determined) Integer of the gasPrice used for each paid gas
     *  - value:
     *    (optional) Integer of the value sent with this transaction
     *  - data:
     *    The compiled code of a contract OR the hash of the invoked method signature and encoded parameters.
     *    For details @see https://github.com/ethereum/wiki/wiki/Ethereum-Contract-ABI
     *  - nonce:
     *    (optional) Integer of a nonce. This allows to overwrite your own pending transactions that use the same nonce.
     *
     * @return Hash
     * 32 Bytes - the transaction hash, or the zero hash if the transaction is not yet available.
     * Use eth_getTransactionReceipt to get the contract address, after the transaction was mined, when you created a contract.
     *
     * @throws \Exception
     */
    public function sendTransaction(Transaction $transaction): Hash
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$transaction->toArray()]));
        return Hash::init($response);

    }

    /**
     * Creates new message call transaction or a contract creation for signed transactions.
     *
     * @param Byte $data
     * The signed transaction data.
     *
     * @return Hash
     * 32 Bytes - the transaction hash, or the zero hash if the transaction is not yet available.
     * Use eth_getTransactionReceipt to get the contract address, after the transaction was mined, when you created a contract.
     *
     * @throws \Exception
     */
    public function sendRawTransaction(Byte $data): Hash
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [Utils::ensureHexPrefix($data->getHex())]));
        return Hash::init($response);

    }

    /**
     * Executes a new message call immediately without creating a transaction on the block chain.
     *
     * @param Transaction $transaction
     * The transaction object
     *  - from:
     *    20 Bytes - (optional) The address the transaction is sent from.
     *  - to:
     *    20 Bytes - The address the transaction is directed to.
     *  - gas:
     *    (optional) Integer of the gas provided for the transaction execution.
     *    eth_call consumes zero gas, but this parameter may be needed by some executions.
     *  - gasPrice:
     *    (optional) Integer of the gasPrice used for each paid gas
     *  - value:
     *    (optional) Integer of the value sent with this transaction
     *  - data:
     *    (optional) Hash of the method signature and encoded parameters.
     *    For details @see https://github.com/ethereum/wiki/wiki/Ethereum-Contract-ABI
     *  - nonce:
     *    (optional) Integer of a nonce. This allows to overwrite your own pending transactions that use the same nonce.
     *
     * @param BlockNumber $blockNumber
     * Integer block number, or the string "latest", "earliest" or "pending", @see https://github.com/ethereum/wiki/wiki/JSON-RPC#the-default-block-parameter
     *
     * @return string
     * The return value of executed contract.
     *
     * @throws \Exception
     */
    public function call(Transaction $transaction, BlockNumber $blockNumber): string
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$transaction->toArray(), $blockNumber->toString()]));
        return $response;
    }

    /**
     * Generates and returns an estimate of how much gas is necessary to allow the transaction to complete.
     * The transaction will not be added to the blockchain.
     * Note that the estimate may be significantly more than the amount of gas actually used by the transaction,
     * for a variety of reasons including EVM mechanics and node performance.
     *
     * @param Transaction $transaction
     * See eth_call parameters, expect that all properties are optional.
     * If no gas limit is specified geth uses the block gas limit from the pending block as an upper bound.
     * As a result the returned estimate might not be enough to executed the call/transaction when the amount of gas is higher than the pending block gas limit.
     *
     * @return Uint
     * The amount of gas used.
     *
     * @throws \Exception
     */
    public function estimateGas(Transaction $transaction): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$transaction->toArray()]));
        return Uint::initWithHex($response);
    }

    /**
     * Returns information about a block by hash.
     *
     * @param Hash $hash
     * 32 Bytes - Hash of a block.
     *
     * @param bool $expandTransactions
     * If true it returns the full transaction objects, if false only the hashes of the transactions.
     *
     * @return Block|null
     * A block object, or null when no block was found.
     */
    public function getBlockByHash(Hash $hash, bool $expandTransactions = false): ?Block
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$hash->toString(), $expandTransactions]));
        return ! empty($response) ? new Block($response) : null;

    }

    /**
     * Returns information about a block by block number.
     *
     * @param BlockNumber $blockNumber
     * Integer of a block number, or the string "earliest", "latest" or "pending", as in the default block parameter.
     *
     * @param bool $expandTransactions
     * If true it returns the full transaction objects, if false only the hashes of the transactions.
     *
     * @return Block|null
     * See eth_getBlockByHash
     */
    public function getBlockByNumber(BlockNumber $blockNumber, bool $expandTransactions = false): ?Block
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$blockNumber->toString(), $expandTransactions]));
        return ! empty($response) ? new Block($response) : null;
    }

    /**
     * Returns the information about a transaction requested by transaction hash.
     *
     * @param Hash $hash
     * 32 Bytes - hash of a transaction
     *
     * @return TransactionInfo|null
     * A transaction object, or null when no transaction was found.
     */
    public function getTransactionByHash(Hash $hash): ?TransactionInfo
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$hash->toString()]));
        return ! empty($response) ? new TransactionInfo($response) : null;
    }

    /**
     * Returns information about a transaction by block hash and transaction index position.
     *
     * @param Hash $hash
     * 32 Bytes - hash of a block.
     *
     * @param Uint $index
     * Integer of the transaction index position.
     *
     * @return TransactionInfo|null
     * See eth_getTransactionByHash
     */
    public function getTransactionByBlockHashAndIndex(Hash $hash, Uint $index): ?TransactionInfo
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$hash->toString(), Utils::ensureHexPrefix($index->getHex())]));
        return ! empty($response) ? new TransactionInfo($response) : null;
    }

    /**
     * Returns information about a transaction by block number and transaction index position.
     *
     * @param BlockNumber $blockNumber
     * A block number, or the string "earliest", "latest" or "pending", as in the default block parameter.
     *
     * @param Uint $index
     * The transaction index position.
     *
     * @return TransactionInfo|null
     * See eth_getTransactionByHash
     */
    public function getTransactionByBlockNumberAndIndex(BlockNumber $blockNumber, Uint $index): ?TransactionInfo
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$blockNumber->toString(), Utils::ensureHexPrefix($index->getHex())]));
        return ! empty($response) ? new TransactionInfo($response) : null;

    }

    /**
     * Returns the receipt of a transaction by transaction hash.
     *
     * @param Hash $hash
     * 32 Bytes - hash of a transaction
     *
     * @return TransactionReceipt|null
     * A transaction receipt object, or null when no receipt was found.
     */
    public function getTransactionReceipt(Hash $hash): ?TransactionReceipt
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$hash->toString()]));
        return ! empty($response) ? new TransactionReceipt($response) : null;

    }

    /**
     * Returns information about a uncle of a block by hash and uncle index position.
     *
     * @param Hash $hash
     * 32 Bytes - hash a block.
     *
     * @param Uint $index
     * The uncle's index position.
     *
     * @return Block|null
     * See eth_getBlockByHash
     */
    public function getUncleByBlockHashAndIndex(Hash $hash, Uint $index): ?Block
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$hash->toString(), Utils::ensureHexPrefix($index->getHex())]));
        return ! empty($response) ? new Block($response) : null;
    }

    /**
     * Returns information about a uncle of a block by number and uncle index position.
     *
     * @param BlockNumber $blockNumber
     * A block number, or the string "earliest", "latest" or "pending", as in the default block parameter.
     *
     * @param Uint $index
     * The uncle's index position.
     *
     * @return Block|null
     * See eth_getBlockByHash
     */
    public function getUncleByBlockNumberAndIndex(BlockNumber $blockNumber, Uint $index): ?Block
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$blockNumber->toString(), Utils::ensureHexPrefix($index->getHex())]));
        return ! empty($response) ? new Block($response) : null;

    }

    /**
     * Returns a list of available compilers in the client.
     *
     * @return string[]
     * Array of available compilers.
     */
    public function getCompilers(): array
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, []));
        return ! empty($response) ? $response : [];

    }

    /**
     * Returns compiled solidity code.
     *
     * @param string $code
     * The source code.
     *
     * @return array
     * The compiled source code.
     */
    public function compileSolidity(string $code): array
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$code]));
        return ! empty($response) ? $response : [];
    }

    /**
     * Creates a filter object, based on filter options, to notify when the state changes (logs). To check if the state has changed, call eth_getFilterChanges.
     *
     * A note on specifying topic filters:
     * Topics are order-dependent. A transaction with a log with topics [A, B] will be matched by the following topic filters:
     *  - [] "anything"
     *  - [A] "A in first position (and anything after)"
     *  - [null, B] "anything in first position AND B in second position (and anything after)"
     *  - [A, B] "A in first position AND B in second position (and anything after)"
     *  - [[A, B], [A, B]] "(A OR B) in first position AND (A OR B) in second position (and anything after)"
     *
     * @param Filter $filter
     * The filter options:
     *  - fromBlock:
     *    (optional, default: "latest") Integer block number, or "latest" for the last mined block or "pending", "earliest" for not yet mined transactions.
     *  - toBlock:
     *    (optional, default: "latest") Integer block number, or "latest" for the last mined block or "pending", "earliest" for not yet mined transactions.
     *  - address:
     *    (optional) Contract address or a list of addresses from which logs should originate.
     *  - topics:
     *    (optional) Array of 32 Bytes DATA topics. Topics are order-dependent. Each topic can also be an array of DATA with "or" options.
     *
     * @return Uint
     * A filter id.
     *
     * @throws \Exception
     */
    public function newFilter(Filter $filter): Uint
    {
        $response = $this->_send($this->_request(1, __FUNCTION__, [$filter->toArray()]));
        return Uint::initWithHex($response);
    }

    /**
     * Uninstalls a filter with given id. Should always be called when watch is no longer needed.
     * Additonally Filters timeout when they aren't requested with eth_getFilterChanges for a period of time.
     *
     * @param Uint $filterId
     * The filter id.
     *
     * @return bool
     * True if the filter was successfully uninstalled, otherwise false.
     */
    public function uninstallFilter(Uint $filterId): bool
    {
        $response = $this->_send($this->_request(73, __FUNCTION__, [Utils::ensureHexPrefix($filterId->getHex())]));
        return (bool)$response;
    }

    /**
     * Polling method for a filter, which returns an array of logs which occurred since last poll.
     *
     * @param Uint $filterId
     * The filter id.
     *
     * @return LogCollection
     * Array of log objects, or an empty array if nothing has changed since last poll.
     *
     * For filters created with eth_newBlockFilter the return are block hashes (DATA, 32 Bytes), e.g. ["0x3454645634534..."].
     * For filters created with eth_newPendingTransactionFilter the return are transaction hashes (DATA, 32 Bytes), e.g. ["0x6345343454645..."].
     * For filters created with eth_newFilter logs are objects with following params:
     *  - removed:
     *    True when the log was removed, due to a chain reorganization. false if its a valid log.
     *  - logIndex:
     *    Integer of the log index position in the block. null when its pending log.
     *  - transactionIndex:
     *    Integer of the transactions index position log was created from. null when its pending log.
     *  - transactionHash:
     *    32 Bytes - hash of the transactions this log was created from. null when its pending log.
     *  - blockHash:
     *    32 Bytes - hash of the block where this log was in. null when its pending. null when its pending log.
     *  - blockNumber:
     *    The block number where this log was in. null when its pending. null when its pending log.
     *  - address:
     *    20 Bytes - address from which this log originated.
     *  - data:
     *    Contains one or more 32 Bytes non-indexed arguments of the log.
     *  - topics:
     *    Array of 0 to 4 32 Bytes DATA of indexed log arguments. (In solidity: The first topic is the hash of the signature of the event
     *    (e.g. Deposit(address,bytes32,uint256)), except you declared the event with the anonymous specifier.)
     */
    public function getFilterChanges(Uint $filterId): LogCollection
    {
        $response = $this->_send($this->_request(73, __FUNCTION__, [Utils::ensureHexPrefix($filterId->getHex())]));
        return new LogCollection($response);
    }

    // @todo: missing methods
}
