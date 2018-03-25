<?php

namespace Ethereum\Methods;

use Ethereum\Types\Uint;

class Net extends AbstractMethods
{
    /**
     * Returns the current network id.
     * 
     * @return Uint
     * The current network id.
     */
    public function version(): Uint
    {
        $response = $this->_send($this->_request(67, __FUNCTION__, []));
        return Uint::init($response);
    }

    /**
     * Returns true if client is actively listening for network connections.
     *
     * @return bool
     * true when listening, otherwise false.
     */
    public function listening(): bool
    {
        $response = $this->_send($this->_request(67, __FUNCTION__, []));
        return (bool)$response;
    }

    /**
     * Returns number of peers currently connected to the client.
     *
     * @return Uint
     * Integer of the number of connected peers.
     *
     * @throws \Exception
     */
    public function peerCount(): Uint
    {
        $response = $this->_send($this->_request(67, __FUNCTION__, []));
        return Uint::initWithHex($response);
    }
}
