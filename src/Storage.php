<?php

namespace Ethereum;

class Storage implements StorageInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string $name
     * @param string $value
     * @return StorageInterface
     */
    public function set(string $name, string $value): StorageInterface
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @param string|null $name
     * @return string
     */
    public function get(string $name): ?string
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * @param string $name
     * @param int $increment
     * @return float
     */
    public function increment(string $name, $increment = 1): float
    {
        if (! isset($this->data[$name])) {
            $this->data[$name] = $increment;
        } else {
            $this->data[$name] += $increment;
        }
        return (float)$this->data[$name];
    }

    /**
     * @param string $name
     * @param int $increment
     * @return float
     */
    public function decrement(string $name, $increment = 1): float
    {
        if (! isset($this->data[$name])) {
            $this->data[$name] = -$increment;
        } else {
            $this->data[$name] -= $increment;
        }
        return (float)$this->data[$name];
    }

    /**
     * @param string $name
     * @return void
     */
    public function remove(string $name): void
    {
        unset($this->data[$name]);
    }
}