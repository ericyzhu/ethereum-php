# Ethereum Client for PHP

PHP 版本以太坊 JSON RPC 客户端。

可通过简单的添加合约地址和 ABI 来方便的调用合约内的方法，对于需要对交易签名的请求，客户端会自动完成。
`pure` 和 `view` 的方法，可以直接以数组的形式返回反序列化后的数据；`nonpayable` 和 `payable` 的方法返回交易的哈希（`\Ethereum\Types\Hash` 实例）。

支持对事件（Log）的监听，但需要通过定时器执行 `\Ethereum\Client::synchronizer->sync()` 方法来轮询，
当有事件到达会执行自定义的回调并传递一个 `\Ethereum\Types\Event` 的实例，该实例包含了反序列化后的事件输入和相关的区块、交易数据。

## JSON RPC API 实现度

已实现

* eth_*
* net_*
* web3_*

未实现

* shh_*

## 依赖

```
php-64bit: ^7.2
ext-gmp: ^7.2
ext-scrypt: ^1.4
ext-secp256k1: ^0.1.0
graze/guzzle-jsonrpc: ^3.2
bitwasp/buffertools: ^0.5.0
```

* ext-scrypt: [https://github.com/DomBlack/php-scrypt](https://github.com/DomBlack/php-scrypt)
* ext-secp256k1: [https://github.com/Bit-Wasp/secp256k1-php](https://github.com/Bit-Wasp/secp256k1-php)

## 使用

### 安装

```
composer require ericychu/ethereum-php
```

### 示例

```
// 实例化以太坊客户端
$client = new Ethereum\Client(
    // JSON RPC 地址
    'https://api.infura.io/v1/jsonrpc/ropsten',
    // 以太坊网络 ID
    3,
    // 节点账户的 Keystore
    '',
    // Keystore 的密码
    '',
    // 存储实例，用来保存一些状态值，可以通过实现 \Ethereum\StorageInterface 接口使用你自己的存储
    new \Ethereum\Storage
);

// 添加合约
$client->contracts
    ->add(
        // 合约别名
        'test_contract',
        // 合约地址
        '',
        // 合约 ABI
        ''
    );

// 监听一个事件，这里的事件名称是你在合约中定义的事件名称。注意，监听事件需要通过定时器执行 $client->synchronizer->sync() 方法来轮询以太坊节点
$client->contracts->test_contract->watch('Event1', function (\Ethereum\Types\Event $data) {
    var_dump($data);
});

// 调用合约中的的方法
var_dump($client->contracts->test_contract->call('test_function', 'test_arg_1', 'test_arg_2'));

// 如果你使用 Swoole，可以通过 Swoole 的定时器来来轮询
swoole_timer_tick(1000, function() use ($client) {
    $client->synchronizer->sync();
});

// 调用 JSON API
echo $client->eth()->protocolVersion();
echo $client->web3()->clientVersion();
echo $client->net()->version();
```