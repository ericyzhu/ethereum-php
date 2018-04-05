<?php

namespace Ethereum\Crypto;

class Keccak
{
    /**
     * @param string $input
     * @param int $outputLength
     * @param bool $rawOutput
     * @return string
     */
    public static function hash(string $input, int $outputLength = 256, bool $rawOutput = false)
    {
        return keccak_hash($input, $outputLength, $rawOutput);
    }
}