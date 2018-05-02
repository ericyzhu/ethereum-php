<?php

namespace Ethereum\Methods;

use Ethereum\Exceptions\RpcResponseErrorException;
use Graze\GuzzleHttp\JsonRpc\ClientInterface;
use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;

abstract class AbstractMethods
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     * @throws RpcResponseErrorException
     */
    protected function _send(RequestInterface $request)
    {
        $response = $this->client->send($request);
        if ($errorCode = $response->getRpcErrorCode()) {
            throw new RpcResponseErrorException($response->getRpcErrorMessage(), $errorCode);
        }
        $result = $response->getRpcResult();
        return $result;
    }

    /**
     * @param string $method
     * @param array|null $params
     * @return RequestInterface
     */
    protected function _request(string $method, ?array $params = null)
    {
        $class = explode('\\', get_class($this));
        $request = $this->client->request(1, strtolower(array_pop($class)).'_'.$method, $params);
        return $request;
    }
}
