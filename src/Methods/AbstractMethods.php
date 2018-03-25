<?php

namespace Ethereum\Methods;

use Graze\GuzzleHttp\JsonRpc\ClientInterface;
use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;

abstract class AbstractMethods
{
    /**
     * @var ClientInterface
     */
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    protected function _send(RequestInterface $request)
    {
        $response = $this->client->send($request);
        $result = $response->getRpcResult();
        return $result;
    }

    protected function _request(int $id, string $method, ?array $params = null)
    {
        $class = explode('\\', get_class($this));
        $request = $this->client->request($id, strtolower(array_pop($class)).'_'.$method, $params);
        return $request;
    }
}
