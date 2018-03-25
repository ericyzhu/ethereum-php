<?php

namespace Ethereum\Methods;

class Web3 extends AbstractMethods
{
    /**
     * Returns the current client version.
     *
     * @return string
     * The current client version
     */
    public function clientVersion(): string
    {
        $response = $this->client->send($this->client->request(67, __FUNCTION__, []));
        return $response;
    }

    /**
     * Returns Keccak-256 (not the standardized SHA3-256) of the given data.
     *
     * @param string $stringToConvert
     * The data to convert into a SHA3 hash
     *
     * @return string
     * The SHA3 result of the given string.
     */
    public function sha3(string $stringToConvert): string
    {
        $response = $this->_send($this->_request(64, __FUNCTION__, [$stringToConvert]));
        return $response;
    }
}
