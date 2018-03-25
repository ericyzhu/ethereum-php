# Ethereum Client for PHP
## 依赖
```
php-64bit: ^7.2
ext-gmp: ^7.2
ext-scrypt: ^1.4  
ext-secp256k1: ^0.1.0
graze/guzzle-jsonrpc: ^3.2
bitwasp/buffertools: ^0.5.0
```
ext-script: [https://github.com/DomBlack/php-scrypt](https://github.com/DomBlack/php-scrypt)

ext-secp256k1: [https://github.com/Bit-Wasp/secp256k1-php](https://github.com/Bit-Wasp/secp256k1-php)
## 使用
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
// $client->eth();
// $client->web3();
// $client->net();
```