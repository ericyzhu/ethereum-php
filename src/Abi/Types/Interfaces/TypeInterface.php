<?php

namespace Ethereum\Abi\Types\Interfaces;

interface TypeInterface
{
    const PADDING_BYTES = 32;

    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data): string;

    /**
     * @param string $data
     * @return mixed
     */
    public function deserialize(string $data);

    /**
     * @param mixed $data
     * @return string
     */
    public function hexize($data): string;

    /**
     * @param mixed $data
     * @return string
     */
    public function hexizeWithPrefix($data): string;

    /**
     * @param string $data
     * @return mixed
     */
    public function dehexize(string $data);

    /**
     * @return bool
     */
    public function isDynamic(): bool;
}
