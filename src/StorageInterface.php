<?php

namespace Ethereum;

interface StorageInterface
{
    /**
     * @param string $name
     * @param string $value
     * @return StorageInterface
     */
    public function set(string $name, string $value): StorageInterface;

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name): ?string;

    /**
     * @param string $name
     * @param int|float $increment
     * @return double
     */
    public function increment(string $name, $increment = 1): float;

    /**
     * @param string $name
     * @param int|float $increment
     * @return float
     */
    public function decrement(string $name, $increment = 1): float;

    /**
     * @param string $name
     * @return void
     */
    public function remove(string $name): void;
}